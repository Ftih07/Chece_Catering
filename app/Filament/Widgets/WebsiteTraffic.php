<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Visit; // Mengimpor model Visit untuk berinteraksi dengan tabel kunjungan.
use Illuminate\Support\Carbon; // Mengimpor kelas Carbon untuk manipulasi tanggal dan waktu.

/**
 * Class WebsiteTraffic
 *
 * Widget ini menampilkan grafik garis (line chart) yang menunjukkan
 * tren traffic pengunjung website selama 7 hari terakhir.
 * Ini membantu administrator untuk memantau aktivitas kunjungan dari waktu ke waktu.
 *
 * Extends Filament\Widgets\ChartWidget untuk memanfaatkan fungsionalitas
 * dasar widget grafik dari Filament.
 */
class WebsiteTraffic extends ChartWidget
{
    /**
     * $heading
     *
     * Properti statis ini mendefinisikan judul yang akan ditampilkan di atas grafik.
     * Judul ini akan terlihat di dashboard Filament.
     *
     * @var string|null
     */
    protected static ?string $heading = 'Traffic Pengunjung Website';

    /**
     * getData()
     *
     * Metode ini bertanggung jawab untuk mengambil data jumlah kunjungan
     * selama 7 hari terakhir dari database dan memformatnya sesuai dengan
     * struktur yang diharapkan oleh Chart.js (yang digunakan oleh Filament).
     *
     * @return array Array yang berisi data untuk datasets dan labels grafik.
     */
    protected function getData(): array
    {
        // Inisialisasi koleksi kosong untuk menyimpan data jumlah kunjungan.
        $data = collect();
        // Inisialisasi array kosong untuk menyimpan label tanggal (sumbu X).
        $labels = [];

        // Melakukan iterasi mundur dari 6 hari yang lalu hingga hari ini (total 7 hari).
        // `range(6, 0)` akan menghasilkan 6, 5, 4, 3, 2, 1, 0.
        foreach (range(6, 0) as $day) {
            // Menghitung tanggal untuk iterasi saat ini.
            // `Carbon::now()->subDays($day)` akan mendapatkan tanggal dari hari ini dikurangi `$day`.
            // `format('Y-m-d')` memformat tanggal menjadi string 'YYYY-MM-DD' untuk query database.
            $date = Carbon::now()->subDays($day)->format('Y-m-d');

            // Menambahkan label tanggal ke array $labels.
            // `translatedFormat('d M')` memformat tanggal menjadi 'DD Mon' (misal: '24 Jul')
            // dan menerjemahkannya sesuai locale aplikasi (jika diatur).
            $labels[] = Carbon::now()->subDays($day)->translatedFormat('d M');

            // Menghitung jumlah kunjungan untuk tanggal spesifik tersebut.
            // `Visit::whereDate('created_at', $date)->count()` melakukan query ke tabel 'visits'
            // untuk menghitung record yang `created_at`nya sesuai dengan `$date`.
            $data[] = Visit::whereDate('created_at', $date)->count();
        }

        // Mengembalikan data dalam format yang sesuai untuk grafik garis.
        return [
            'datasets' => [ // Array dari datasets (seri data) untuk grafik.
                [
                    'label' => 'Total Visit', // Label untuk seri data ini (akan muncul di legenda grafik).
                    'data' => $data, // Data jumlah kunjungan per hari yang telah dikumpulkan.
                    'borderColor' => '#22c55e', // Warna garis grafik (hijau).
                    'backgroundColor' => 'rgba(34,197,94,0.2)', // Warna area di bawah garis (hijau transparan).
                    'fill' => true, // Mengisi area di bawah garis dengan warna `backgroundColor`.
                    'tension' => 0.4, // Mengatur ketegangan garis, membuat garis terlihat lebih mulus (kurva).
                ],
            ],
            'labels' => $labels, // Label untuk sumbu X (tanggal-tanggal).
        ];
    }

    /**
     * getType()
     *
     * Metode ini mendefinisikan tipe grafik yang akan dirender.
     *
     * @return string Tipe grafik (misal: 'line', 'bar', 'pie', 'doughnut').
     */
    protected function getType(): string
    {
        return 'line'; // Mengatur tipe grafik menjadi 'line' (grafik garis).
    }
}
