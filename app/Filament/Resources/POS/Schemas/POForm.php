<?php

namespace App\Filament\Resources\POS\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class POForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // 🔹 DATA UTAMA PO
                Section::make('Informasi PO')
                    ->schema([
                        TextInput::make('nomor_po')
                            ->label('Nomor PO')
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('Akan dibuat otomatis setelah disimpan'),

                        TextInput::make('versi_po')
                            ->label('Versi PO')
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('Otomatis dibuat'),

                        Select::make('id_pengajuan')
                            ->label('Perusahaan / Pengajuan')
                            ->relationship('kerjasama', 'nama_perusahaan')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(fn($record) => $record !== null) // 🔒 lock saat edit
                            ->dehydrated(fn($record) => $record === null), // hanya kirim saat create
                    ])
                    ->columns(1),

                // 🔹 HARGA
                Section::make('Harga')
                    ->schema([
                        TextInput::make('harga_penawaran')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('Rp')
                            ->required(),

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

                                        // 🔥 Tidak boleh lebih dari sisa hutang
                                        if ($value > $sisa) {
                                            $fail("Jumlah bayar melebihi sisa tagihan. Sisa: Rp " . number_format($sisa, 0, ',', '.'));
                                        }

                                        // 🔥 Tidak boleh kurang dari harga_deal (total keseluruhan harus = harga_deal)
                                        // Artinya: pembayaran terakhir harus menutup tepat, tidak boleh kurang jika ini cicilan terakhir
                                        // ATAU: setiap bayar minimal = harga_deal sekali bayar lunas
                                        // Pilih logic sesuai kebutuhan:

                                        // ✅ OPSI A: jumlah_bayar tidak boleh kurang dari harga_deal (harus lunas sekaligus)
                                        if ($value < $po->harga_deal) {
                                            $fail("Jumlah bayar tidak boleh kurang dari harga deal. Harga deal: Rp " . number_format($po->harga_deal, 0, ',', '.'));
                                        }

                                        // ✅ OPSI B: jumlah_bayar harus tepat sama dengan sisa (tidak boleh kurang, tidak boleh lebih)
                                        // if ($value != $sisa) {
                                        //     $fail("Jumlah bayar harus tepat Rp " . number_format($sisa, 0, ',', '.'));
                                        // }
                                    };
                                }
                            ])
                            ->validationMessages([
                                'required' => 'Jumlah bayar wajib diisi',
                                'min'      => 'Jumlah bayar harus lebih dari 0',
                                'numeric'  => 'Jumlah bayar harus berupa angka',
                            ]),
                    ])
                    ->columns(1),

                // 🔹 TANGGAL
                Section::make('Tanggal')
                    ->schema([
                        DatePicker::make('tanggal_po')
                            ->required(),

                        DatePicker::make('tanggal_mulai')
                            ->required(),

                        DatePicker::make('tanggal_selesai')
                            ->required()
                            ->afterOrEqual('tanggal_mulai') // 🔥 tidak boleh sebelum mulai
                            ->validationMessages([
                                'after_or_equal' => 'Tanggal selesai harus setelah tanggal mulai',
                            ]),
                    ])
                    ->columns(1),

                // 🔹 STATUS
                Section::make('Status')
                    ->schema([
                        Select::make('status_po')
                            ->options([
                                'draft' => 'Draft',
                                'diajukan' => 'Diajukan',
                                'revisi' => 'Revisi',
                                'final' => 'Final',
                                'ditolak' => 'Ditolak',
                            ])
                            ->required()
                            ->default('draft'),

                        Select::make('status_kerjasama')
                            ->options([
                                'negosiasi' => 'Negosiasi',
                                'deal' => 'Deal',
                                'batal' => 'Batal',
                                'proses' => 'Proses',
                                'selesai' => 'Selesai',
                            ])
                            ->required()
                            ->default('negosiasi'),
                    ])
                    ->columns(1),

                // 🔹 FILE
                Section::make('Dokumen')
                    ->schema([
                        FileUpload::make('dokumen_po')
                            ->label('Dokumen PO')
                            ->directory('po')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(2048)
                            ->multiple(false)
                            ->required()
                            ->downloadable()              // 🔥 tombol download
                            ->openable()                  // 🔥 tombol buka/preview
                            ->fetchFileInformation(false) // 🔥 stop loading terus-terusan
                            ->rules(['mimes:pdf'])
                            ->validationMessages([
                                'required' => 'Dokumen PO wajib diupload',
                                'mimes'    => 'Dokumen PO harus berformat PDF',
                                'max'      => 'Ukuran file maksimal 2MB',
                            ]),

                        FileUpload::make('dokumen_invoice')
                            ->label('Invoice')
                            ->directory('invoice')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(2048)
                            ->multiple(false)
                            ->downloadable()              // 🔥 tombol download
                            ->openable()                  // 🔥 tombol buka/preview
                            ->fetchFileInformation(false) // 🔥 stop loading terus-terusan
                            ->rules(['mimes:pdf'])
                            ->visible(fn($get) => $get('status_po') === 'final')
                            ->required(fn($get) => $get('status_po') === 'final')
                            ->validationMessages([
                                'required' => 'Invoice wajib diupload jika PO sudah final',
                                'mimes'    => 'Invoice harus berformat PDF',
                                'max'      => 'Ukuran file maksimal 2MB',
                            ]),
                    ])
                    ->columns(1),

                // 🔹 KETERANGAN
                Section::make('Keterangan')
                    ->schema([
                        Textarea::make('keterangan')
                            ->rows(3)
                            ->maxLength(1000),
                    ]),
            ]);
    }
}
