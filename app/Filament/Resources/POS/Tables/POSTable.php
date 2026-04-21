<?php

namespace App\Filament\Resources\POS\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class POSTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nomor_po')
                    ->label('Nomor PO')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('versi_po')
                    ->label('Versi')
                    ->badge()
                    ->color('info'),

                TextColumn::make('harga_penawaran')
                    ->label('Penawaran')
                    ->money('IDR', true)
                    ->sortable(),

                TextColumn::make('harga_deal')
                    ->label('Deal')
                    ->money('IDR', true)
                    ->sortable(),

                TextColumn::make('tanggal_po')
                    ->date()
                    ->label('Tanggal PO'),

                TextColumn::make('tanggal_mulai')
                    ->date()
                    ->label('Mulai'),

                TextColumn::make('tanggal_selesai')
                    ->date()
                    ->label('Selesai'),

                BadgeColumn::make('status_po')
                    ->colors([
                        'warning' => 'draft',
                        'success' => 'disetujui',
                        'danger' => 'ditolak',
                    ]),

                BadgeColumn::make('status_pembayaran')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'lunas',
                        'danger' => 'belum bayar',
                    ]),

                TextColumn::make('dokumen_po')
                    ->label('Dokumen PO')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state ? 'Tersedia' : 'Dokumen Kosong')
                    ->color(fn($state) => $state ? 'success' : 'danger'),

                TextColumn::make('dokumen_invoice')
                    ->label('Invoice')
                    ->badge()
                    ->getStateUsing(fn($record) => $record->dokumen_invoice ?? 'kosong')
                    ->formatStateUsing(fn($state) => $state !== 'kosong' ? 'Tersedia' : 'Dokumen Kosong')
                    ->color(fn($state) => $state !== 'kosong' ? 'success' : 'danger'),

                TextColumn::make('bukti_pembayaran')
                    ->label('Bukti Bayar')
                    ->badge()
                    ->getStateUsing(fn($record) => $record->bukti_pembayaran ?? 'kosong')
                    ->formatStateUsing(fn($state) => $state !== 'kosong' ? 'Tersedia' : 'Dokumen Kosong')
                    ->color(fn($state) => $state !== 'kosong' ? 'success' : 'danger'),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()
                        ->label('Edit')
                        ->icon('heroicon-o-pencil-square')
                        ->color('primary'),

                    ViewAction::make()
                        ->label('Lihat')
                        ->icon('heroicon-o-eye')
                        ->color('gray'),


                    DeleteAction::make()
                        ->label('Hapus')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Cabang?')
                        ->modalDescription('Purchase Order akan dihapus permanen dan tidak dapat dikembalikan.')
                        ->modalSubmitActionLabel('Ya, Hapus')
                        ->successNotification(
                            Notification::make()
                                ->title('Berhasil')
                                ->body('Purchase Order berhasil dihapus.')
                                ->success()
                        ),

                ])
                    ->label('Aksi')
                    ->icon('heroicon-o-ellipsis-vertical')
                    ->button()
                    ->outlined()
                    ->tooltip('Aksi')
                    ->dropdownPlacement('bottom-end')
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
