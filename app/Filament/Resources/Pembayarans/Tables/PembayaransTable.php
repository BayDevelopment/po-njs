<?php

namespace App\Filament\Resources\Pembayarans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class PembayaransTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('po.nomor_po')
                    ->label('Nomor PO')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('termin')
                    ->label('Termin ke-')
                    ->badge()
                    ->sortable(),

                TextColumn::make('jumlah_bayar')
                    ->label('Jumlah Bayar')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('tanggal_pembayaran')
                    ->label('Tanggal Bayar')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('metode')
                    ->label('Metode')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'transfer' => 'info',
                        'tunai'    => 'success',
                        'cek'      => 'warning',
                        'giro'     => 'gray',
                        default    => 'gray',
                    }),

                TextColumn::make('po.harga_deal')
                    ->label('Total PO')
                    ->money('IDR'),

                // 🔥 Sisa pembayaran (computed)
                TextColumn::make('sisa_pembayaran')
                    ->label('Sisa')
                    ->money('IDR')
                    ->getStateUsing(function ($record) {
                        $totalBayar = $record->po
                            ->pembayaran()
                            ->sum('jumlah_bayar');
                        return max(0, $record->po->harga_deal - $totalBayar);
                    })
                    ->color(fn($state) => $state > 0 ? 'danger' : 'success'),

                TextColumn::make('po.status_pembayaran')
                    ->label('Status')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'unpaid'  => 'danger',
                        'partial' => 'warning',
                        'paid'    => 'success',
                        default   => 'gray',
                    }),

                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(30)
                    ->tooltip(fn($record) => $record->keterangan),
            ])->defaultSort('tanggal_pembayaran', 'desc') // terbaru di atas

            ->filters([
                SelectFilter::make('metode')
                    ->options([
                        'transfer' => 'Transfer',
                        'tunai'    => 'Tunai',
                        'cek'      => 'Cek',
                        'giro'     => 'Giro',
                    ]),

                SelectFilter::make('po.status_pembayaran')
                    ->label('Status Pembayaran')
                    ->options([
                        'unpaid'  => 'Belum Bayar',
                        'partial' => 'Sebagian',
                        'paid'    => 'Lunas',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
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
