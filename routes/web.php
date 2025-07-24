<?php

use App\Http\Controllers\GalleryController; // Mengimpor GalleryController untuk mengelola rute galeri.
use App\Http\Controllers\HomeController;    // Mengimpor HomeController untuk mengelola rute halaman beranda.
use App\Http\Controllers\MenuController;    // Mengimpor MenuController untuk mengelola rute menu.
use Illuminate\Support\Facades\Route;       // Mengimpor Facade Route untuk mendefinisikan rute web.

/**
 * File: routes/web.php
 *
 * File ini adalah tempat di mana semua rute (URL) yang diakses melalui browser web
 * didefinisikan untuk aplikasi Laravel Anda. Setiap rute memetakan URL ke
 * sebuah tindakan (action) di controller atau sebuah closure (fungsi anonim).
 */

// Rute untuk halaman beranda (homepage).
// Ketika pengguna mengakses URL dasar aplikasi ('/'), permintaan akan ditangani
// oleh metode `index` di `HomeController`.
// `->name('home')` memberikan nama unik pada rute ini, yang berguna untuk
// menghasilkan URL dalam aplikasi (misal: `route('home')`).
Route::get('/', [HomeController::class, 'index'])->name('home');

// Rute untuk halaman galeri.
// Ketika pengguna mengakses URL '/gallery', permintaan akan ditangani
// oleh metode `index` di `GalleryController`.
// `->name('gallery')` memberikan nama unik pada rute ini.
Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery');

// Rute untuk halaman daftar menu.
// Ketika pengguna mengakses URL '/menu', permintaan akan ditangani
// oleh metode `index` di `MenuController`.
// `->name('menu')` memberikan nama unik pada rute ini.
Route::get('/menu', [MenuController::class, 'index'])->name('menu');

// Rute pengujian error.
// Rute ini hanya untuk tujuan pengujian dan debugging.
// Ketika pengguna mengakses URL seperti '/test-error/404' atau '/test-error/500',
// ia akan memicu HTTP exception dengan kode status yang diberikan.
// Ini berguna untuk menguji halaman error kustom yang telah Anda buat.
Route::get('/test-error/{code}', function ($code) {
    // `abort((int) $code)` akan menghentikan eksekusi aplikasi
    // dan memicu HTTP exception dengan kode status yang dikonversi dari parameter URL.
    abort((int) $code);
});
