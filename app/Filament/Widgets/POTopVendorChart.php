<?php

namespace App\Filament\Widgets;

use App\Models\POModel;
use Filament\Widgets\ChartWidget;

class POTopVendorChart extends ChartWidget
{
    protected ?string $heading = 'Top 5 Vendor · Nilai Deal Tertinggi';

    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        /**
         * Ambil top 5 via relasi kerjasama → nama_perusahaan.
         * Sesuaikan nama kolom dengan model KerjasamaModel kamu.
         *
         * Asumsi KerjasamaModel punya kolom: nama_perusahaan
         */
        $top = POModel::query()
            ->with('kerjasama')                     // eager load
            ->selectRaw('id_pengajuan, SUM(harga_deal) as total_deal')
            ->where('status_kerjasama', '!=', 'batal')
            ->groupBy('id_pengajuan')
            ->orderByDesc('total_deal')
            ->limit(5)
            ->get();

        $labels = $top->map(function ($po) {
            // Fallback jika relasi null
            return optional($po->kerjasama)->nama_perusahaan
                ?? 'ID-' . $po->id_pengajuan;
        })->toArray();

        $values = $top->map(function ($po) {
            return round($po->total_deal / 1_000_000, 1); // Juta Rp
        })->toArray();

        $colors = [
            'rgba(99,102,241,0.8)',
            'rgba(16,185,129,0.8)',
            'rgba(245,158,11,0.8)',
            'rgba(239,68,68,0.8)',
            'rgba(20,184,166,0.8)',
        ];

        return [
            'datasets' => [
                [
                    'label'           => 'Total Deal (Juta Rp)',
                    'data'            => $values,
                    'backgroundColor' => $colors,
                    'borderRadius'    => 8,
                    'borderSkipped'   => false,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',   // horizontal bar — lebih enak untuk nama panjang
            'plugins'   => [
                'legend' => ['display' => false],
            ],
            'scales' => [
                'x' => [
                    'title' => [
                        'display' => true,
                        'text'    => 'Nilai (Juta Rp)',
                    ],
                    'grid' => ['color' => 'rgba(0,0,0,0.05)'],
                ],
                'y' => [
                    'grid' => ['display' => false],
                    'ticks' => ['font' => ['weight' => '600']],
                ],
            ],
        ];
    }
}
