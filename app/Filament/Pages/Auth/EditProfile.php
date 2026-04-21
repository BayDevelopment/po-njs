<?php

namespace App\Filament\Pages\Auth;

use Filament\Actions\Action;
use Filament\Auth\Pages\EditProfile as BaseEditProfile; // ← namespace V5 yang benar

class EditProfile extends BaseEditProfile
{
    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()
            ->label('Simpan')
            ->icon('heroicon-o-check');
    }

    protected function getCancelFormAction(): Action
    {
        return Action::make('back')
            ->label('Kembali')
            ->icon('heroicon-o-arrow-left')
            ->color('gray')
            ->alpineClickHandler('document.referrer ? window.history.back() : (window.location.href = \'/po\')');
    }
}
