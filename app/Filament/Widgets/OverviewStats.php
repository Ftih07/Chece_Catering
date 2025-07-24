<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Models\Menu;
use App\Models\MenuCategory; // Meskipun diimpor, MenuCategory tidak digunakan dalam kode ini.
use App\Models\Gallery;
use Filament\Support\Colors\Color; // Meskipun diimpor, Color tidak digunakan secara langsung (hanya string warna).

use App\Models\Visit;
use Carbon\Carbon; // Digunakan secara implisit oleh helper `today()` dan `now()`.

/**
 * Class OverviewStats
 *
 * Widget ini menampilkan ringkasan statistik penting dalam bentuk kartu (cards)
 * di dashboard Filament. Statistik yang ditampilkan meliputi jumlah total menu,
 * jumlah gambar galeri, dan statistik kunjungan (harian, mingguan, bulanan, total).
 *
 * Extends Filament\Widgets\StatsOverviewWidget untuk memanfaatkan fungsionalitas
 * dasar widget statistik Filament.
 */
class OverviewStats extends StatsOverviewWidget
{
    /**
     * getCards()
     *
     * Metode ini bertanggung jawab untuk mengumpulkan data dan mendefinisikan
     * kartu-kartu statistik yang akan ditampilkan di widget.
     * Setiap kartu mewakili metrik tertentu dengan judul, nilai, deskripsi, warna, dan ikon.
     *
     * @return array Array dari objek Card, masing-masing mewakili satu kartu statistik.
     */
    protected function getCards(): array
    {
        // Menghitung jumlah kunjungan untuk hari ini.
        // Menggunakan `whereDate('created_at', today())` untuk memfilter berdasarkan tanggal saja.
        $todayVisits = Visit::whereDate('created_at', today())->count();

        // Menghitung jumlah kunjungan untuk minggu ini.
        // Menggunakan `whereBetween()` untuk memfilter kunjungan dari awal minggu hingga akhir minggu saat ini.
        $weekVisits = Visit::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();

        // Menghitung jumlah kunjungan untuk bulan ini.
        // Menggunakan `whereMonth()` dan `whereYear()` untuk memfilter kunjungan di bulan dan tahun saat ini.
        $monthVisits = Visit::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();

        // Menghitung total seluruh kunjungan yang pernah tercatat.
        $totalVisits = Visit::count();

        // Mengembalikan array yang berisi definisi setiap kartu statistik.
        return [
            // Kartu untuk menampilkan total jumlah menu.
            Card::make('Total Menu', Menu::count())
                ->description('Jumlah semua menu yang tersedia') // Deskripsi singkat untuk kartu.
                ->color('success') // Warna kartu (menggunakan nama warna Tailwind/Filament).
                ->icon('heroicon-o-clipboard-document'), // Ikon yang akan ditampilkan di kartu (dari Heroicons).

            // Kartu untuk menampilkan total jumlah gambar di galeri.
            Card::make('Galeri', Gallery::count())
                ->description('Total gambar galeri')
                ->color('warning')
                ->icon('heroicon-o-photo'),

            // Kartu untuk menampilkan jumlah kunjungan hari ini.
            Card::make('Kunjungan Hari Ini', $todayVisits)
                ->description('Pengunjung yang datang hari ini')
                ->color('info')
                ->icon('heroicon-o-eye'),

            // Kartu untuk menampilkan total kunjungan minggu ini.
            Card::make('Kunjungan Minggu Ini', $weekVisits)
                ->description('Total kunjungan minggu ini')
                ->color('primary')
                ->icon('heroicon-o-calendar-days'),

            // Kartu untuk menampilkan total kunjungan bulan ini.
            Card::make('Kunjungan Bulan Ini', $monthVisits)
                ->description('Total kunjungan bulan ini')
                ->color('indigo')
                ->icon('heroicon-o-calendar'),

            // Kartu untuk menampilkan total seluruh kunjungan.
            Card::make('Total Kunjungan', $totalVisits)
                ->description('Jumlah semua pengunjung')
                ->color('gray')
                ->icon('heroicon-o-chart-bar'),
        ];
    }
}
