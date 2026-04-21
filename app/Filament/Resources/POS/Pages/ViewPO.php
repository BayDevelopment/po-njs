<?php

namespace App\Filament\Resources\POS\Pages;

use App\Filament\Resources\POS\POResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPO extends ViewRecord
{
    protected static string $resource = POResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
