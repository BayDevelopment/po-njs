<?php

namespace App\Filament\Resources\POS;

use App\Filament\Resources\POS\Pages\CreatePO;
use App\Filament\Resources\POS\Pages\EditPO;
use App\Filament\Resources\POS\Pages\ListPOS;
use App\Filament\Resources\POS\Pages\ViewPO;
use App\Filament\Resources\POS\Schemas\POForm;
use App\Filament\Resources\POS\Schemas\POInfolist;
use App\Filament\Resources\POS\Tables\POSTable;
use App\Models\PO;
use App\Models\POModel;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class POResource extends Resource
{
    protected static ?string $model = POModel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $recordTitleAttribute = 'id_pengajuan';

    // ADD
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    public static function getNavigationGroup(): ?string
    {
        return 'Master Data';
    }
    public static function getModelLabel(): string
    {
        return 'Manajemen PO';
    }
    public static function getPluralModelLabel(): string
    {
        return 'Data Manajemen PO';
    }
    protected static ?string $navigationLabel = 'Manajemen PO';
    protected static ?int    $navigationSort  = 1;
    // LAST ADD

    public static function form(Schema $schema): Schema
    {
        return POForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return POInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return POSTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPOS::route('/'),
            'create' => CreatePO::route('/create'),
            'view' => ViewPO::route('/{record}'),
            'edit' => EditPO::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
