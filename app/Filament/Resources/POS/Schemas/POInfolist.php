<?php

namespace App\Filament\Resources\POS\Schemas;

use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Infolists\Components\TextEntry;

class POInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Informasi Purchase Order')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        TextEntry::make('status_po')
                            ->label('Status PO')
                            ->badge()
                            ->color(fn($state) => match ($state) {
                                'diajukan'  => 'warning',
                                'disetujui' => 'success',
                                'ditolak'   => 'danger',
                                default     => 'gray',
                            }),

                        TextEntry::make('keterangan')
                            ->label('Keterangan')
                            ->columnSpanFull()
                            ->placeholder('Tidak ada keterangan')
                            ->color('gray'),
                    ]),

                Section::make('Informasi Pembayaran')
                    ->icon('heroicon-o-banknotes')
                    ->schema([
                        TextEntry::make('status_pembayaran')
                            ->label('Status Pembayaran')
                            ->badge()
                            ->color(fn($state) => match ($state) {
                                'paid'    => 'success',
                                'unpaid'  => 'danger',
                                'partial' => 'warning',
                                default   => 'gray',
                            }),

                        TextEntry::make('tanggal_pembayaran')
                            ->label('Tanggal Pembayaran')
                            ->date('d M Y')
                            ->placeholder('Belum dibayar'),
                    ]),

                Section::make('Dokumen')
                    ->icon('heroicon-o-paper-clip')
                    ->columnSpanFull()  // ← ini
                    ->columns(3)
                    ->schema([
                        TextEntry::make('dokumen_po')
                            ->label('Dokumen PO')
                            ->badge()
                            ->getStateUsing(fn($record) => $record->dokumen_po ?? 'kosong')
                            ->formatStateUsing(fn($state) => $state !== 'kosong' ? 'Tersedia' : 'Dokumen Kosong')
                            ->color(fn($state) => $state !== 'kosong' ? 'success' : 'danger'),

                        TextEntry::make('dokumen_invoice')
                            ->label('Invoice')
                            ->badge()
                            ->getStateUsing(fn($record) => $record->dokumen_invoice ?? 'kosong')
                            ->formatStateUsing(fn($state) => $state !== 'kosong' ? 'Tersedia' : 'Dokumen Kosong')
                            ->color(fn($state) => $state !== 'kosong' ? 'success' : 'danger'),

                        TextEntry::make('bukti_pembayaran')
                            ->label('Bukti Pembayaran')
                            ->badge()
                            ->getStateUsing(fn($record) => $record->bukti_pembayaran ?? 'kosong')
                            ->formatStateUsing(fn($state) => $state !== 'kosong' ? 'Tersedia' : 'Dokumen Kosong')
                            ->color(fn($state) => $state !== 'kosong' ? 'success' : 'danger'),
                    ]),

            ]);
    }
}
