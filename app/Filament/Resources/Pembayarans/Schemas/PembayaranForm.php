<?php

namespace App\Filament\Resources\Pembayarans\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use App\Models\POModel;

class PembayaranForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // ── PILIH PO ─────────────────────────────
                Select::make('id_po')
                    ->label('Pilih PO')
                    ->relationship(
                        name: 'po',
                        titleAttribute: 'nomor_po',
                        modifyQueryUsing: fn($query) => $query
                            ->where('status_kerjasama', 'selesai')
                            ->where('status_po', 'final')
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->disabled(fn($record) => $record !== null)
                    ->dehydrated(fn($record) => $record === null)
                    ->validationMessages([
                        'required' => 'PO wajib dipilih',
                    ]),

                // ── TERMIN ───────────────────────────────
                TextInput::make('termin')
                    ->label('Termin Ke')
                    ->placeholder('Cicilan 1 atau DP 1')
                    ->numeric()
                    ->required()
                    ->helperText('Isi 1 jika bayar lunas sekaligus, atau sesuai urutan cicilan (DP=1, Pelunasan=2, dst)')
                    ->minValue(1)
                    ->maxValue(99)
                    ->rule(function (callable $get, $record) { // ← tambah $record
                        return function (string $attribute, $value, \Closure $fail) use ($get, $record) {
                            $idPo = $get('id_po');
                            if (!$idPo) return;

                            $exists = \App\Models\PembayaranModel::withoutTrashed()
                                ->where('id_po', $idPo)
                                ->where('termin', $value)
                                ->when($record, fn($q) => $q->where('id_pembayaran', '!=', $record->id_pembayaran)) // ← ignore record ini
                                ->exists();

                            if ($exists) {
                                $fail("Termin ke-{$value} untuk PO ini sudah ada.");
                            }
                        };
                    })
                    ->validationMessages([
                        'required' => 'Termin wajib diisi',
                        'min'      => 'Termin minimal 1',
                        'max'      => 'Termin maksimal 99',
                        'numeric'  => 'Termin harus berupa angka',
                    ]),

                // ── JUMLAH BAYAR ─────────────────────────
                TextInput::make('jumlah_bayar')
                    ->label('Jumlah Bayar')
                    ->numeric()
                    ->required()
                    ->prefix('Rp')
                    ->minValue(1)
                    ->rules([
                        function () {
                            return function (string $attribute, $value, \Closure $fail) {
                                $idPo = request()->input('data.id_po');
                                if (!$idPo) return;

                                $po = \App\Models\POModel::find($idPo);
                                if (!$po) return;

                                $totalBayar = \App\Models\PembayaranModel::where('id_po', $idPo)
                                    ->sum('jumlah_bayar');

                                $sisa = $po->harga_deal - $totalBayar;

                                if ($value > $sisa) {
                                    $fail("Jumlah bayar melebihi sisa tagihan. Sisa: Rp " . number_format($sisa, 0, ',', '.'));
                                }
                            };
                        }
                    ]),

                // ── TANGGAL PEMBAYARAN ───────────────────
                DatePicker::make('tanggal_pembayaran')
                    ->label('Tanggal Pembayaran')
                    ->required()
                    ->maxDate(now()) // 🔥 tidak boleh tanggal masa depan
                    ->validationMessages([
                        'required' => 'Tanggal pembayaran wajib diisi',
                        'before_or_equal' => 'Tanggal pembayaran tidak boleh masa depan',
                    ]),

                // ── METODE ───────────────────────────────
                Select::make('metode')
                    ->options([
                        'transfer' => 'Transfer',
                        'tunai'    => 'Tunai',
                        'cek'      => 'Cek',
                        'giro'     => 'Giro',
                    ])
                    ->required()
                    ->validationMessages([
                        'required' => 'Metode pembayaran wajib dipilih',
                    ]),

                Select::make('status_pembayaran')
                    ->label('Status Pembayaran')
                    ->options([
                        'unpaid'  => 'Unpaid',
                        'partial' => 'Partial',
                        'paid'    => 'Paid',
                    ])
                    ->default('unpaid')
                    ->required()
                    ->native(false)
                    ->validationMessages([
                        'required' => 'Status pembayaran wajib dipilih',
                    ]),

                // ── BUKTI PEMBAYARAN ─────────────────────
                FileUpload::make('bukti_pembayaran')
                    ->label('Bukti Pembayaran')
                    ->directory('bukti-pembayaran')
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'application/pdf'])
                    ->maxSize(2048)
                    ->required() // 🔥 wajib upload bukti
                    ->rules(['mimes:jpg,jpeg,png,pdf'])
                    ->validationMessages([
                        'required' => 'Bukti pembayaran wajib diupload',
                        'mimes'    => 'Format harus JPG, PNG, atau PDF',
                        'max'      => 'Ukuran file maksimal 2MB',
                    ]),

                // ── KETERANGAN ───────────────────────────
                Textarea::make('keterangan')
                    ->rows(3)
                    ->maxLength(500)
                    ->validationMessages([
                        'max' => 'Keterangan maksimal 500 karakter',
                    ]),

            ]);
    }
}
