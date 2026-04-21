<?php

namespace App\Filament\Resources\POS\Pages;

use App\Filament\Resources\POS\POResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPOS extends ListRecords
{
    protected static string $resource = POResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Purchase Order')
                ->icon('heroicon-o-plus'),
        ];
    }
}
