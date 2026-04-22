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

                        TextInput::make('harga_deal')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('Rp')
                            ->required()
                            ->lte('harga_penawaran') // 🔥 tidak boleh lebih besar dari penawaran
                            ->validationMessages([
                                'lte' => 'Harga deal tidak boleh lebih besar dari penawaran',
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
