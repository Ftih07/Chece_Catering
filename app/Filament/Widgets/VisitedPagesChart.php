<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Visit; // Mengimpor model Visit untuk berinteraksi dengan tabel kunjungan.

/**
 * Class VisitedPagesChart
 *
 * Widget ini berfungsi untuk menampilkan grafik (chart) yang menunjukkan
 * halaman-halaman mana saja yang paling sering dikunjungi di aplikasi.
 * Ini membantu administrator untuk melihat popularitas halaman tertentu.
 *
 * Extends Filament\Widgets\ChartWidget untuk memanfaatkan fungsionalitas
 * dasar widget grafik dari Filament.
 */
class VisitedPagesChart extends ChartWidget
{
    /**
     * $heading
     *
     * Properti statis ini mendefinisikan judul yang akan ditampilkan di atas grafik.
     * Judul ini akan terlihat di dashboard Filament.
     *
     * @var string|null
     */
    protected static ?string $heading = 'Halaman Paling Sering Dikunjungi';

    /**
     * getData()
     *
     * Metode ini bertanggung jawab untuk mengambil data dari database
     * dan memformatnya sesuai dengan struktur yang diharapkan oleh Chart.js
     * (yang digunakan oleh Filament untuk merender grafik).
     *
     * @return array Array yang berisi data untuk datasets dan labels grafik.
     */
    protected function getData(): array
    {
        // Mengambil 5 halaman teratas berdasarkan jumlah kunjungan.
        // Query ini akan:
        // 1. Memilih kolom 'path' (jalur URL) dan menghitung total kunjungan untuk setiap jalur.
        // 2. Mengelompokkan hasil berdasarkan 'path' (groupBy('path')).
        // 3. Mengurutkan hasil secara menurun berdasarkan 'total' kunjungan (orderByDesc('total')).
        // 4. Membatasi hasil hanya untuk 5 entri teratas (limit(5)).
        // 5. Mengeksekusi query dan mendapatkan hasilnya (get()).
        $topPages = Visit::selectRaw('path, COUNT(*) as total')
            ->groupBy('path')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // Mengembalikan data dalam format yang sesuai untuk grafik.
        return [
            'datasets' => [ // Array dari datasets (seri data) untuk grafik.
                [
                    'label' => 'Jumlah Kunjungan', // Label untuk seri data ini (akan muncul di legenda grafik).
                    'data' => $topPages->pluck('total'), // Mengambil hanya nilai 'total' dari koleksi $topPages. Ini akan menjadi tinggi bar pada grafik.
                    'backgroundColor' => [ // Array warna untuk setiap bar atau segmen pada grafik.
                        '#10b981', // Hijau (success)
                        '#3b82f6', // Biru (primary)
                        '#f59e0b', // Kuning (warning)
                        '#ef4444', // Merah (danger)
                        '#8b5cf6', // Ungu (indigo)
                    ],
                ],
            ],
            'labels' => $topPages->pluck('path'), // Mengambil hanya nilai 'path' dari koleksi $topPages. Ini akan menjadi label di sumbu X (atau label segmen pada pie/doughnut).
        ];
    }

    /**
     * getType()
     *
     * Metode ini mendefinisikan tipe grafik yang akan dirender.
     *
     * @return string Tipe grafik (misal: 'bar', 'pie', 'doughnut', 'line').
     */
    protected function getType(): string
    {
        return 'bar'; // Mengatur tipe grafik menjadi 'bar' (grafik batang).
        // Anda bisa mengubahnya menjadi 'pie' atau 'doughnut' untuk grafik lingkaran.
    }
}
