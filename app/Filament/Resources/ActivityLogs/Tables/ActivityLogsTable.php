<?php

namespace App\Filament\Resources\ActivityLogs\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ActivityLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('causer.name')
                    ->label('User')
                    ->default('System')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('description')
                    ->label('Aksi')
                    ->badge()
                    ->color(fn($state) => match (true) {
                        str_contains($state, 'dibuat')    => 'success',
                        str_contains($state, 'diperbarui') => 'warning',
                        str_contains($state, 'dihapus')   => 'danger',
                        default                            => 'gray',
                    }),

                TextColumn::make('subject_type')
                    ->label('Model')
                    ->formatStateUsing(fn($state) => class_basename($state ?? '')),

                TextColumn::make('properties')
                    ->label('Perubahan')
                    ->wrap()
                    ->html()
                    ->formatStateUsing(function ($state) {
                        if (!$state) return '-';

                        if (is_string($state)) {
                            $props = json_decode($state, true);
                        } elseif (is_array($state)) {
                            $props = $state;
                        } else {
                            $props = $state->toArray();
                        }

                        // ← Coba kedua kemungkinan struktur
                        $old = $props['old'] ?? [];
                        $new = $props['attributes'] ?? $props['new'] ?? [];

                        // Kalau tidak ada 'attributes', bandingkan old vs sisa key
                        if (empty($new)) {
                            // Struktur flat: {"old": {...}, "metode": "...", "termin": ...}
                            $new = collect($props)
                                ->except(['old'])
                                ->toArray();
                        }

                        if (empty($new)) return '-';

                        return collect($new)
                            ->map(function ($val, $key) use ($old) {
                                $before = $old[$key] ?? '-';
                                if ($before === $val) return null; // skip yang tidak berubah
                                return "<span style='color:#64748b;font-size:11px'>{$key}</span>: " .
                                    "<span style='color:#ef4444'>{$before}</span>" .
                                    " → " .
                                    "<span style='color:#22c55e;font-weight:600'>{$val}</span>";
                            })
                            ->filter() // hapus null
                            ->join('<br>') ?: '-';
                    }),

                TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y • H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('description')
                    ->label('Aksi')
                    ->options([
                        'PO dibuat'              => 'PO Dibuat',
                        'PO diperbarui'          => 'PO Diperbarui',
                        'PO dihapus'             => 'PO Dihapus',
                        'Pembayaran ditambahkan' => 'Pembayaran Ditambahkan',
                        'Pembayaran diperbarui'  => 'Pembayaran Diperbarui',
                        'Pembayaran dihapus'     => 'Pembayaran Dihapus',
                    ]),
            ])
            ->recordActions([
                ActionGroup::make([
                    DeleteAction::make()
                        ->label('Hapus')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Activity?')
                        ->modalDescription('Activity akan dihapus permanen dan tidak dapat dikembalikan.')
                        ->modalSubmitActionLabel('Ya, Hapus')
                        ->successNotification(
                            Notification::make()
                                ->title('Berhasil')
                                ->body('Activity berhasil dihapus.')
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
                    DeleteBulkAction::make(), // ← hapus massal
                ]),
            ]);
    }

    // 🔥 TARUH DI SINI (dalam class, bukan di luar)
    protected static function formatLabel($key)
    {
        return match ($key) {
            'jumlah_bayar'      => 'Jumlah Bayar',
            'status_pembayaran' => 'Status Pembayaran',
            'metode'            => 'Metode',
            'termin'            => 'Termin',
            'keterangan'        => 'Keterangan',
            default             => ucfirst(str_replace('_', ' ', $key)),
        };
    }
}
