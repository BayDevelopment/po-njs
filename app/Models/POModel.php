<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class POModel extends Model
{
    use SoftDeletes, LogsActivity;

    protected $connection  = 'mysql_ci';
    protected $table       = 'tb_po';
    protected $primaryKey  = 'id_po';

    protected $fillable = [
        'id_pengajuan',
        'versi_po',
        'nomor_po',
        'dokumen_po',
        'harga_penawaran',
        'harga_deal',
        'tanggal_po',
        'tanggal_mulai',
        'tanggal_selesai',
        'status_po',
        'status_kerjasama',
        'dokumen_invoice',
        'status_pembayaran',
        'keterangan',
    ];

    protected $casts = [
        'dokumen_po'      => 'string',
        'dokumen_invoice' => 'string',
        'tanggal_po'      => 'date',
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
        'harga_penawaran' => 'integer',
        'harga_deal'      => 'integer',
    ];

    // ── ACTIVITY LOG ──────────────────────────────
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status_po', 'status_kerjasama', 'harga_deal', 'tanggal_po'])
            ->logOnlyDirty();
    }

    // POModel.php & PembayaranModel.php

    public function tapActivity(\Spatie\Activitylog\Contracts\Activity $activity, string $eventName): void
    {
        $activity->description = match ($eventName) {
            'created' => 'PO dibuat',
            'updated' => 'PO diperbarui',
            'deleted' => 'PO dihapus',
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
    public function kerjasama()
    {
        return $this->belongsTo(
            KerjasamaModel::class,
            'id_pengajuan',
            'id_pengajuan'
        );
    }

    public function pembayaran()
    {
        return $this->hasMany(PembayaranModel::class, 'id_po');
    }

    // ── BOOTED ───────────────────────────────────
    protected static function booted()
    {
        static::creating(function ($model) {
            $year  = date('Y');
            $romawi = [
                1 => 'I',
                2 => 'II',
                3 => 'III',
                4 => 'IV',
                5 => 'V',
                6 => 'VI',
                7 => 'VII',
                8 => 'VIII',
                9 => 'IX',
                10 => 'X',
                11 => 'XI',
                12 => 'XII',
            ];
            $bulan = $romawi[(int) date('m')];

            $lastNomor  = self::withTrashed()->whereYear('created_at', $year)->max('nomor_po');
            $lastNumber = 0;

            if ($lastNomor) {
                preg_match('/PO\/(\d+)\//', $lastNomor, $matches);
                $lastNumber = isset($matches[1]) ? (int) $matches[1] : 0;
            }

            do {
                $lastNumber++;
                $nomor = 'PO/' . str_pad($lastNumber, 3, '0', STR_PAD_LEFT) . '/' . $bulan . '/' . $year;
            } while (self::withTrashed()->where('nomor_po', $nomor)->exists());

            $model->nomor_po = $nomor;

            $lastVersion    = self::withTrashed()->where('id_pengajuan', $model->id_pengajuan)->max('versi_po');
            $model->versi_po = $lastVersion ? $lastVersion + 1 : 1;
        });
    }
}
