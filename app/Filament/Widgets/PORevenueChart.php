<?php

namespace App\Filament\Widgets;

use App\Models\PembayaranModel;
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

        // ── 1. NILAI DEAL (KONTRAK) ─────────────────────────
        $nilaiDeal = collect(range(1, 12))->map(function ($bulan) use ($year) {
            return (float) POModel::whereYear('tanggal_po', $year)
                ->whereMonth('tanggal_po', $bulan)
                ->whereIn('status_po', ['final', 'diajukan'])
                ->sum('harga_deal') / 1_000_000;
        })->toArray();

        // ── 2. NILAI PENAWARAN ──────────────────────────────
        $nilaiPenawaran = collect(range(1, 12))->map(function ($bulan) use ($year) {
            return (float) POModel::whereYear('tanggal_po', $year)
                ->whereMonth('tanggal_po', $bulan)
                ->sum('harga_penawaran') / 1_000_000;
        })->toArray();

        // ── 3. PEMBAYARAN REAL (INI YANG BARU & BENAR) 🔥 ───
        $nilaiPembayaran = collect(range(1, 12))->map(function ($bulan) use ($year) {
            return (float) POModel::whereYear('tanggal_po', $year)
                ->whereMonth('tanggal_po', $bulan)
                ->where('status_pembayaran', 'paid')
                ->sum('harga_deal') / 1_000_000;
        })->toArray();

        // ── 4. JUMLAH PO ────────────────────────────────────
        $jumlahPO = collect(range(1, 12))->map(function ($bulan) use ($year) {
            return POModel::whereYear('tanggal_po', $year)
                ->whereMonth('tanggal_po', $bulan)
                ->count();
        })->toArray();

        return [
            'datasets' => [

                // DEAL
                [
                    'label' => 'Harga Deal (Juta Rp)',
                    'data' => $nilaiDeal,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16,185,129,0.12)',
                    'fill' => true,
                    'tension' => 0.4,
                    'yAxisID' => 'y',
                    'type' => 'line',
                ],

                // PENAWARAN
                [
                    'label' => 'Harga Penawaran (Juta Rp)',
                    'data' => $nilaiPenawaran,
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245,158,11,0.08)',
                    'fill' => false,
                    'tension' => 0.4,
                    'borderDash' => [5, 5],
                    'yAxisID' => 'y',
                    'type' => 'line',
                ],

                // 🔥 PEMBAYARAN REAL (INI YANG PALING PENTING)
                [
                    'label' => 'Pembayaran Real (Juta Rp)',
                    'data' => $nilaiPembayaran,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59,130,246,0.12)',
                    'fill' => true,
                    'tension' => 0.4,
                    'yAxisID' => 'y',
                    'type' => 'line',
                ],

                // JUMLAH PO
                [
                    'label' => 'Jumlah PO',
                    'data' => $jumlahPO,
                    'backgroundColor' => 'rgba(99,102,241,0.25)',
                    'borderColor' => 'rgba(99,102,241,0.6)',
                    'borderRadius' => 6,
                    'yAxisID' => 'y1',
                    'type' => 'bar',
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
