<?php

namespace App\Filament\Resources\POS\Pages;

use App\Filament\Resources\POS\POResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreatePO extends CreateRecord
{
    protected static string $resource = POResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // 🔒 paksa abaikan nomor_po dari frontend
        unset($data['nomor_po']);

        // 🔒 validasi id_pengajuan
        $exists = \App\Models\KerjasamaModel::where('id_pengajuan', $data['id_pengajuan'])->exists();

        if (! $exists) {
            abort(403, 'id_pengajuan atau perusahaan tidak sesuai');
        }

        return $data;
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Berhasil')
            ->body('Purchase Order berhasil ditambahkan.')
            ->success();
    }

    protected function getFormActions(): array
    {
        return [

            $this->getCreateFormAction()
                ->label('Create')
                ->icon('heroicon-o-check-circle')
                ->color('primary'),

            $this->getCreateAnotherFormAction()
                ->label('Create & Create Another')
                ->icon('heroicon-o-plus-circle')
                ->color('success'),

            $this->getCancelFormAction()
                ->label('Cancel')
                ->url($this->getResource()::getUrl('index'))
                ->icon('heroicon-o-x-mark')
                ->color('gray'),

        ];
    }
}
