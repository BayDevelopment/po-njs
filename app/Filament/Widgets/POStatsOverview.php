<?php

namespace App\Filament\Widgets;

use App\Models\PembayaranModel;
use App\Models\POModel;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class POStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $now = Carbon::now();

        // ── Total semua PO ──────────────────────────
        $totalPO = POModel::count();

        // ── PO Bulan Ini ────────────────────────────
        $poBulanIni = POModel::whereMonth('tanggal_po', $now->month)
            ->whereYear('tanggal_po', $now->year)
            ->count();

        // ── PO Berjalan ─────────────────────────────
        $poBerjalan = POModel::whereIn('status_po', ['diajukan', 'final'])
            ->where('status_kerjasama', 'deal')
            ->count();

        // ── PO Selesai ──────────────────────────────
        $poSelesai = POModel::where('status_kerjasama', 'selesai')->count();

        // ── TOTAL REVENUE (DARI PEMBAYARAN) 🔥 ─────
        $totalRevenue = PembayaranModel::sum('jumlah_bayar');

        // ── TREND PO ───────────────────────────────
        $trendBulanan = collect(range(5, 0))->map(function ($i) use ($now) {
            return POModel::whereMonth('tanggal_po', $now->copy()->subMonths($i)->month)
                ->whereYear('tanggal_po', $now->copy()->subMonths($i)->year)
                ->count();
        })->toArray();

        // ── TREND REVENUE 🔥 ───────────────────────
        $trendRevenue = collect(range(5, 0))->map(function ($i) use ($now) {
            return (int) PembayaranModel::whereMonth(
                'tanggal_pembayaran',
                $now->copy()->subMonths($i)->month
            )
                ->whereYear(
                    'tanggal_pembayaran',
                    $now->copy()->subMonths($i)->year
                )
                ->sum('jumlah_bayar');
        })->toArray();

        // ── PERBANDINGAN BULAN ─────────────────────
        $poBulanLalu = POModel::whereMonth('tanggal_po', $now->copy()->subMonth()->month)
            ->whereYear('tanggal_po', $now->copy()->subMonth()->year)
            ->count();

        $trendLabel = $poBulanLalu > 0
            ? ($poBulanIni >= $poBulanLalu
                ? '+' . round((($poBulanIni - $poBulanLalu) / $poBulanLalu) * 100) . '% dari bulan lalu'
                : round((($poBulanIni - $poBulanLalu) / $poBulanLalu) * 100) . '% dari bulan lalu')
            : 'Tidak ada data bulan lalu';

        $trendColor = $poBulanIni >= $poBulanLalu ? 'success' : 'danger';

        return [

            Stat::make('Total Purchase Order', number_format($totalPO))
                ->description('Semua PO terdaftar')
                ->descriptionIcon('heroicon-m-document-text')
                ->chart($trendBulanan)
                ->color('primary'),

            Stat::make('PO Bulan Ini', number_format($poBulanIni))
                ->description($trendLabel)
                ->descriptionIcon(
                    $poBulanIni >= $poBulanLalu
                        ? 'heroicon-m-arrow-trending-up'
                        : 'heroicon-m-arrow-trending-down'
                )
                ->chart($trendBulanan)
                ->color($trendColor),

            Stat::make('PO Sedang Berjalan', number_format($poBerjalan))
                ->description('Status diajukan / final · Kerjasama deal')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('warning'),

            Stat::make('PO Selesai', number_format($poSelesai))
                ->description('Revenue: Rp ' . number_format($totalRevenue, 0, ',', '.'))
                ->descriptionIcon('heroicon-m-check-badge')
                ->chart($trendRevenue)
                ->color('success'),
        ];
    }
}
