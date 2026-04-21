<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Dashboard';

    protected static array $widgets = [
        \App\Filament\Widgets\POStatsOverview::class,
        \App\Filament\Widgets\PORevenueChart::class,
        \App\Filament\Widgets\POStatusDonutChart::class,
        \App\Filament\Widgets\POTopVendorChart::class,
    ];
}
