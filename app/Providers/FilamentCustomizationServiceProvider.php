<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider; // Mengimpor kelas dasar ServiceProvider dari Laravel.
use Filament\Panel;                     // Mengimpor kelas Panel dari Filament (meskipun tidak digunakan secara langsung di sini).
use Filament\Facades\Filament;          // Mengimpor Facade Filament untuk berinteraksi dengan Filament.

/**
 * Class FilamentCustomizationServiceProvider
 *
 * Service Provider ini digunakan untuk melakukan kustomisasi pada panel admin Filament.
 * Secara spesifik, provider ini bertanggung jawab untuk menambahkan ikon kustom (favicon)
 * ke bagian head HTML dari setiap halaman panel Filament.
 */
class FilamentCustomizationServiceProvider extends ServiceProvider
{
    /**
     * register()
     *
     * Metode `register` digunakan untuk mengikat kelas ke dalam container layanan Laravel.
     * Dalam kasus ini, tidak ada layanan yang spesifik yang perlu didaftarkan.
     *
     * @return void
     */
    public function register(): void
    {
        // Tidak ada implementasi khusus yang diperlukan di sini untuk saat ini.
    }

    /**
     * boot()
     *
     * Metode `boot` dipanggil setelah semua service provider lainnya terdaftar
     * dan di-boot. Ini adalah tempat yang tepat untuk mendaftarkan event listener,
     * routes, atau melakukan kustomisasi yang memerlukan akses ke layanan yang sudah ada.
     *
     * @return void
     */
    public function boot(): void
    {
        // Mendaftarkan callback yang akan dieksekusi saat Filament sedang "serving" (memuat)
        // sebuah panel. Ini memastikan kustomisasi diterapkan hanya ketika panel Filament aktif.
        Filament::serving(function () {
            // Mendaftarkan "render hook" ke Filament.
            // Render hook memungkinkan kita untuk menyuntikkan (inject) konten HTML
            // ke lokasi tertentu dalam struktur DOM Filament.
            Filament::registerRenderHook(
                'panels::head.end', // 1. Lokasi hook: Ini berarti konten akan disuntikkan
                //    tepat sebelum tag `</head>` ditutup di halaman panel Filament.
                fn(): string => '<link rel="icon" type="image/png" href="' . asset('assets/images/logo/logo.png') . '">'
                // 2. Callback fungsi: Mengembalikan string HTML dari tag <link> untuk favicon.
                //    `asset('assets/images/logo/logo.png')` menghasilkan URL lengkap ke ikon gambar.
            );
        });
    }
}
