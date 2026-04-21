<?php

namespace App\Filament\Widgets;

use App\Models\POModel;
use Filament\Widgets\ChartWidget;

class PORevenueChart extends ChartWidget
{
    protected ?string $heading = 'Tren Nilai PO per Bulan (Tahun Ini)';

    protected static ?int $sort = 2;

    // Lebar penuh
    protected int | string | array $columnSpan = 'full';

    // Filter: bisa ganti tahun
    public ?string $filter = null;

    protected function getFilters(): ?array
    {
        $years = [];
        for ($y = now()->year; $y >= now()->year - 4; $y--) {
            $years[(string)$y] = (string)$y;
        }
        return $years;
    }

    protected function getData(): array
    {
        $year = $this->filter ?? now()->year;

        $bulanLabel = [
            'Jan',
            'Feb',
            'Mar',
            'Apr',
            'Mei',
            'Jun',
            'Jul',
            'Agu',
            'Sep',
            'Okt',
            'Nov',
            'Des',
        ];

        // ── Dataset 1 : Nilai harga_deal per bulan ──────────────────────────
        $nilaiDeal = collect(range(1, 12))->map(function ($bulan) use ($year) {
            return (float) POModel::whereYear('tanggal_po', $year)
                ->whereMonth('tanggal_po', $bulan)
                ->whereIn('status_po', ['final', 'diajukan'])
                ->sum('harga_deal') / 1_000_000; // dalam juta rupiah
        })->toArray();

        // ── Dataset 2 : Nilai harga_penawaran per bulan ─────────────────────
        $nilaiPenawaran = collect(range(1, 12))->map(function ($bulan) use ($year) {
            return (float) POModel::whereYear('tanggal_po', $year)
                ->whereMonth('tanggal_po', $bulan)
                ->sum('harga_penawaran') / 1_000_000;
        })->toArray();

        // ── Dataset 3 : Jumlah PO per bulan (sumbu Y kanan via type bar) ────
        $jumlahPO = collect(range(1, 12))->map(function ($bulan) use ($year) {
            return POModel::whereYear('tanggal_po', $year)
                ->whereMonth('tanggal_po', $bulan)
                ->count();
        })->toArray();

        return [
            'datasets' => [
                [
                    'label'           => 'Harga Deal (Juta Rp)',
                    'data'            => $nilaiDeal,
                    'borderColor'     => '#10b981',       // emerald
                    'backgroundColor' => 'rgba(16,185,129,0.12)',
                    'fill'            => true,
                    'tension'         => 0.4,
                    'yAxisID'         => 'y',
                    'type'            => 'line',
                    'pointBackgroundColor' => '#10b981',
                    'pointRadius'     => 5,
                    'pointHoverRadius' => 8,
                ],
                [
                    'label'           => 'Harga Penawaran (Juta Rp)',
                    'data'            => $nilaiPenawaran,
                    'borderColor'     => '#f59e0b',       // amber
                    'backgroundColor' => 'rgba(245,158,11,0.08)',
                    'fill'            => false,
                    'tension'         => 0.4,
                    'yAxisID'         => 'y',
                    'type'            => 'line',
                    'borderDash'      => [5, 5],
                    'pointBackgroundColor' => '#f59e0b',
                    'pointRadius'     => 4,
                ],
                [
                    'label'           => 'Jumlah PO',
                    'data'            => $jumlahPO,
                    'backgroundColor' => 'rgba(99,102,241,0.25)',  // indigo
                    'borderColor'     => 'rgba(99,102,241,0.6)',
                    'borderRadius'    => 6,
                    'yAxisID'         => 'y1',
                    'type'            => 'bar',
                ],
            ],
            'labels' => $bulanLabel,
        ];
    }

    protected function getType(): string
    {
        // "bar" sebagai base, dataset individual boleh override ke "line"
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'responsive'          => true,
            'interaction'         => [
                'mode'      => 'index',
                'intersect' => false,
            ],
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                    'labels'   => [
                        'usePointStyle' => true,
                        'padding'       => 20,
                    ],
                ],
                'tooltip' => [
                    'callbacks' => [
                        // Tooltip diformat di sisi PHP lewat custom JS —
                        // Filament render via ChartJS native, jadi kita pakai suffix
                    ],
                ],
            ],
            'scales' => [
                'x' => [
                    'grid' => ['display' => false],
                    'ticks' => ['font' => ['weight' => '600']],
                ],
                'y' => [
                    'type'     => 'linear',
                    'display'  => true,
                    'position' => 'left',
                    'title'    => [
                        'display' => true,
                        'text'    => 'Nilai (Juta Rp)',
                        'font'    => ['weight' => '600'],
                    ],
                    'grid' => ['color' => 'rgba(0,0,0,0.05)'],
                    'ticks' => [
                        'callback' => 'function(v){return "Rp "+v+"Jt"}',
                    ],
                ],
                'y1' => [
                    'type'     => 'linear',
                    'display'  => true,
                    'position' => 'right',
                    'title'    => [
                        'display' => true,
                        'text'    => 'Jumlah PO',
                        'font'    => ['weight' => '600'],
                    ],
                    'grid' => ['drawOnChartArea' => false],
                    'ticks' => ['stepSize' => 1],
                ],
            ],
        ];
    }
}
