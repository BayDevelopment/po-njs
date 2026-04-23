<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected bool $passwordChanged = false;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Deteksi apakah password diubah
        if (!empty($data['password'])) {
            $this->passwordChanged = true;
        }

        return $data;
    }

    protected function afterSave(): void
    {
        if ($this->passwordChanged) {
            Notification::make()
                ->title('Password berhasil diubah')
                ->body('Silakan login kembali dengan password baru.')
                ->success()
                ->send();

            // Logout setelah 2 detik
            Auth::logout();

            request()->session()->invalidate();
            request()->session()->regenerateToken();

            $this->redirect(route('filament.po.auth.login')); // ← sesuaikan nama panel
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
            ->body('Users berhasil diperbarui.')
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
