<?php

namespace App\Filament\Widgets;

use App\Models\POModel;
use Filament\Widgets\ChartWidget;

class POStatusDonutChart extends ChartWidget
{
    protected ?string $heading  = 'Distribusi Status Kerjasama';

    protected static ?int $sort = 3;

    // Setengah lebar (berdampingan dengan chart lain)
    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        $statuses = ['negosiasi', 'deal', 'batal', 'proses', 'selesai'];

        $counts = collect($statuses)->map(function ($status) {
            return POModel::where('status_kerjasama', $status)->count();
        })->toArray();

        return [
            'datasets' => [
                [
                    'data'            => $counts,
                    'backgroundColor' => [
                        '#94a3b8',  // negosiasi  – slate
                        '#10b981',  // deal       – emerald
                        '#ef4444',  // batal      – red
                        '#f59e0b',  // proses     – amber
                        '#6366f1',  // selesai    – indigo
                    ],
                    'borderWidth'     => 2,
                    'hoverOffset'     => 8,
                ],
            ],
            'labels' => ['Negosiasi', 'Deal', 'Batal', 'Proses', 'Selesai'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'labels'   => [
                        'usePointStyle' => true,
                        'padding'       => 16,
                        'font'          => ['size' => 12],
                    ],
                ],
            ],
            'cutout' => '65%',
        ];
    }
}
