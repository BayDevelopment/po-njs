<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class PembayaranModel extends Model
{
    use SoftDeletes, LogsActivity;

    protected $connection = 'mysql_ci';
    protected $table      = 'tb_pembayaran';
    protected $primaryKey = 'id_pembayaran';

    protected static $logAttributes = ['*']; // 🔥 wajib
    protected static $logOnlyDirty = true;   // 🔥 wajib

    protected $fillable = [
        'id_po',
        'termin',
        'jenis_termin',
        'jumlah_bayar',
        'tanggal_pembayaran',
        'bukti_pembayaran',
        'metode',
        'status_pembayaran',
        'keterangan',
    ];
    protected $casts = [
        'jumlah_bayar' => 'integer',
    ];

    // ── ACTIVITY LOG ──────────────────────────────
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'jumlah_bayar',
                'status_pembayaran',
                'metode',
                'termin',
            ])
            ->logOnlyDirty();
    }
    // POModel.php & PembayaranModel.php

    public function tapActivity(\Spatie\Activitylog\Contracts\Activity $activity, string $eventName): void
    {
        $activity->description = match ($eventName) {
            'created' => 'Pembayaran ditambahkan',
            'updated' => 'Pembayaran diperbarui',
            'deleted' => 'Pembayaran dihapus',
            default   => $eventName,
        };

        // ← Simpan properties manual
        if ($eventName === 'updated') {
            $dirty    = $this->getDirty();
            $original = $this->getOriginal();

            $old = [];
            $new = [];

            foreach ($dirty as $key => $value) {
                $old[$key] = $original[$key] ?? null;
                $new[$key] = $value;
            }

            $activity->properties = collect([
                'old'        => $old,
                'attributes' => $new,
            ]);
        }

        if ($eventName === 'created') {
            $activity->properties = collect([
                'attributes' => $this->getAttributes(),
            ]);
        }
    }

    // ── RELASI ────────────────────────────────────
    public function po()
    {
        return $this->belongsTo(POModel::class, 'id_po', 'id_po');
    }

    // ── BOOTED ───────────────────────────────────
    protected static function booted()
    {
        static::saving(function ($pembayaran) {
            $po = \App\Models\POModel::find($pembayaran->id_po);
            if (!$po) return;

            $totalBayar = self::where('id_po', $pembayaran->id_po)
                ->when($pembayaran->exists, fn($q) => $q->where('id_pembayaran', '!=', $pembayaran->id_pembayaran))
                ->sum('jumlah_bayar');

            $sisa = $po->harga_deal - $totalBayar;

            if ($pembayaran->jumlah_bayar < 1) {
                \Filament\Notifications\Notification::make()
                    ->title('Jumlah Bayar Tidak Valid')
                    ->body('Jumlah bayar harus lebih dari 0.')
                    ->danger()
                    ->send();

                throw \Illuminate\Validation\ValidationException::withMessages([
                    'jumlah_bayar' => 'Jumlah bayar harus lebih dari 0.',
                ]);
            }

            if ($pembayaran->jumlah_bayar > $sisa) {
                \Filament\Notifications\Notification::make()
                    ->title('Jumlah Bayar Melebihi Tagihan')
                    ->body('Jumlah bayar melebihi sisa tagihan. Sisa: Rp ' . number_format($sisa, 0, ',', '.'))
                    ->danger()
                    ->send();

                throw \Illuminate\Validation\ValidationException::withMessages([
                    'jumlah_bayar' => 'Jumlah bayar melebihi sisa tagihan. Sisa: Rp ' . number_format($sisa, 0, ',', '.'),
                ]);
            }
        });

        static::saved(function ($pembayaran) {
            $po         = $pembayaran->po;
            $totalBayar = \App\Models\PembayaranModel::where('id_po', $pembayaran->id_po)
                ->sum('jumlah_bayar');

            $status = match (true) {
                $totalBayar <= 0               => 'unpaid',
                $totalBayar >= $po->harga_deal => 'paid',
                default                        => 'partial',
            };

            $po->update(['status_pembayaran' => $status]);
        });
    }
}
