<?php

namespace App\Filament\Widgets;

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

        // ── Total semua PO (aktif, tidak terhapus) ──────────────────────────
        $totalPO = POModel::count();

        // ── PO Aktif Bulan Ini (berdasarkan tanggal_po) ─────────────────────
        $poBulanIni = POModel::whereMonth('tanggal_po', $now->month)
            ->whereYear('tanggal_po', $now->year)
            ->count();

        // ── PO sedang berjalan: status_po = diajukan | final
        //    DAN status_kerjasama = deal ────────────────────────────────────
        $poBerjalan = POModel::whereIn('status_po', ['diajukan', 'final'])
            ->where('status_kerjasama', 'deal')
            ->count();

        // ── PO Selesai: status_kerjasama = selesai ───────────────────────────
        $poSelesai = POModel::where('status_kerjasama', 'selesai')->count();

        // ── Total nilai harga_deal yang sudah selesai (paid) ─────────────────
        $totalRevenue = POModel::where('status_kerjasama', 'selesai')
            ->where('status_pembayaran', 'paid')
            ->sum('harga_deal');

        // ── Trend 6 bulan terakhir untuk sparkline ──────────────────────────
        $trendBulanan = collect(range(5, 0))->map(function ($i) use ($now) {
            return POModel::whereMonth('tanggal_po', $now->copy()->subMonths($i)->month)
                ->whereYear('tanggal_po', $now->copy()->subMonths($i)->year)
                ->count();
        })->toArray();

        $trendRevenue = collect(range(5, 0))->map(function ($i) use ($now) {
            return (int) POModel::where('status_kerjasama', 'selesai')
                ->where('status_pembayaran', 'paid')
                ->whereMonth('tanggal_pembayaran', $now->copy()->subMonths($i)->month)
                ->whereYear('tanggal_pembayaran', $now->copy()->subMonths($i)->year)
                ->sum('harga_deal');
        })->toArray();

        // ── Hitung persentase naik/turun dibanding bulan lalu ───────────────
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

            // ── CARD 1 : Total Semua PO ─────────────────────────────────────
            Stat::make('Total Purchase Order', number_format($totalPO))
                ->description('Semua PO terdaftar')
                ->descriptionIcon('heroicon-m-document-text')
                ->chart($trendBulanan)
                ->color('primary'),

            // ── CARD 2 : PO Bulan Ini ───────────────────────────────────────
            Stat::make('PO Bulan Ini', number_format($poBulanIni))
                ->description($trendLabel)
                ->descriptionIcon(
                    $poBulanIni >= $poBulanLalu
                        ? 'heroicon-m-arrow-trending-up'
                        : 'heroicon-m-arrow-trending-down'
                )
                ->chart($trendBulanan)
                ->color($trendColor),

            // ── CARD 3 : PO Sedang Berjalan ─────────────────────────────────
            Stat::make('PO Sedang Berjalan', number_format($poBerjalan))
                ->description('Status diajukan / final · Kerjasama deal')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('warning'),

            // ── CARD 4 : PO Selesai + Total Revenue ─────────────────────────
            Stat::make('PO Selesai', number_format($poSelesai))
                ->description('Revenue terbayar: Rp ' . number_format($totalRevenue, 0, ',', '.'))
                ->descriptionIcon('heroicon-m-check-badge')
                ->chart($trendRevenue)
                ->color('success'),
        ];
    }
}
