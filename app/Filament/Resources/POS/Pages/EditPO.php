<?php

namespace App\Filament\Resources\POS\Pages;

use App\Filament\Resources\POS\POResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPO extends EditRecord
{
    protected static string $resource = POResource::class;
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // 🔒 abaikan nomor_po
        unset($data['nomor_po']);

        // 🔒 ambil id asli
        $originalId = $this->record->id_pengajuan;

        // ❗ jika ada manipulasi
        if (isset($data['id_pengajuan']) && $data['id_pengajuan'] != $originalId) {
            abort(403, 'id_pengajuan atau perusahaan tidak sesuai');
        }

        // 🔒 paksa tetap pakai id asli
        $data['id_pengajuan'] = $originalId;

        return $data;
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Berhasil')
            ->body('Purchase Order berhasil diperbarui.')
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
