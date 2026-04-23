<?php

namespace App\Filament\Resources\Pembayarans\Pages;

use App\Filament\Resources\Pembayarans\PembayaranResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditPembayaran extends EditRecord
{
    protected static string $resource = PembayaranResource::class;

    protected array $statusSebelum = []; // ← TAMBAH

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // ← Harus di sini, SEBELUM data disimpan
        $this->statusSebelum = [
            'status_pembayaran' => $this->record->status_pembayaran,
            'jumlah_bayar'      => $this->record->jumlah_bayar,
            'metode'            => $this->record->metode,
            'termin'            => $this->record->termin,
            'keterangan'        => $this->record->keterangan,
        ];

        $data['id_po'] = $this->record->id_po;
        return $data;
    }

    protected function afterSave(): void
    {
        $record = $this->record->fresh();

        $baru = [
            'status_pembayaran' => $record->status_pembayaran,
            'jumlah_bayar'      => $record->jumlah_bayar,
            'metode'            => $record->metode,
            'termin'            => $record->termin,
            'keterangan'        => $record->keterangan,
        ];

        $old     = [];
        $changed = [];

        foreach ($baru as $key => $value) {
            if ((string)($this->statusSebelum[$key] ?? '') !== (string)$value) {
                $old[$key]     = $this->statusSebelum[$key] ?? '-';
                $changed[$key] = $value;
            }
        }

        if (!empty($changed)) {
            activity()
                ->causedBy(Auth::user())
                ->performedOn($record)
                ->withProperties([
                    'old'        => $old,
                    'attributes' => $changed,
                ])
                ->log('Pembayaran diperbarui');
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Berhasil')
            ->body('Pembayaran berhasil diperbarui.')
            ->success();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(static::getResource()::getUrl('index')),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction()
                ->label('Save Changes')
                ->icon('heroicon-o-check-circle')
                ->color('primary'),

            $this->getCancelFormAction()
                ->label('Cancel')
                ->icon('heroicon-o-x-mark')
                ->color('gray')
                ->url(static::getResource()::getUrl('index')),
        ];
    }
}
