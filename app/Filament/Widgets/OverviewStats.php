<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Models\Menu;
use App\Models\MenuCategory;
use App\Models\Gallery;
use Filament\Support\Colors\Color;

use App\Models\Visit;
use Carbon\Carbon;

class OverviewStats extends StatsOverviewWidget
{
    protected function getCards(): array
    {
        $todayVisits = Visit::whereDate('created_at', today())->count();
        $weekVisits = Visit::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $monthVisits = Visit::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();
        $totalVisits = Visit::count();

        return [
            Card::make('Total Menu', Menu::count())
                ->description('Jumlah semua menu yang tersedia')
                ->color('success')
                ->icon('heroicon-o-clipboard-document'),

            Card::make('Galeri', Gallery::count())
                ->description('Total gambar galeri')
                ->color('warning')
                ->icon('heroicon-o-photo'),

            Card::make('Kunjungan Hari Ini', $todayVisits)
                ->description('Pengunjung yang datang hari ini')
                ->color('info')
                ->icon('heroicon-o-eye'),

            Card::make('Kunjungan Minggu Ini', $weekVisits)
                ->description('Total kunjungan minggu ini')
                ->color('primary')
                ->icon('heroicon-o-calendar-days'),

            Card::make('Kunjungan Bulan Ini', $monthVisits)
                ->description('Total kunjungan bulan ini')
                ->color('indigo')
                ->icon('heroicon-o-calendar'),

            Card::make('Total Kunjungan', $totalVisits)
                ->description('Jumlah semua pengunjung')
                ->color('gray')
                ->icon('heroicon-o-chart-bar'),
        ];
    }
}
