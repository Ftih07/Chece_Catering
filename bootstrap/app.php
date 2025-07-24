<?php

use Illuminate\Foundation\Application;               // Mengimpor kelas utama Application Laravel.
use Illuminate\Foundation\Configuration\Exceptions;   // Mengimpor kelas untuk konfigurasi penanganan exception.
use Illuminate\Foundation\Configuration\Middleware;   // Mengimpor kelas untuk konfigurasi middleware.

/**
 * File: bootstrap/app.php
 *
 * File ini adalah titik awal (bootstrap) untuk aplikasi Laravel.
 * Ini bertanggung jawab untuk mengkonfigurasi dan membuat instance aplikasi Laravel,
 * termasuk mendefinisikan rute, middleware, dan penanganan exception.
 *
 * Ini adalah bagian dari struktur aplikasi Laravel 10+ yang menggunakan
 * "Application Configuration" yang lebih fungsional.
 */
return Application::configure(basePath: dirname(__DIR__)) // Menginisialisasi aplikasi Laravel.
    // `basePath: dirname(__DIR__)` mengatur direktori dasar aplikasi
    // ke direktori induk dari file 'bootstrap'.

    // Mengkonfigurasi definisi rute untuk aplikasi.
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',       // Menentukan lokasi file rute web.
        // Semua rute yang diakses melalui browser akan didefinisikan di sini.
        commands: __DIR__ . '/../routes/console.php', // Menentukan lokasi file rute konsol (perintah Artisan).
        // Perintah kustom untuk CLI didefinisikan di sini.
        health: '/up',                              // Menentukan rute endpoint "health check" (`/up`).
        // Digunakan untuk memverifikasi bahwa aplikasi sedang berjalan.
    )

    // Mengkonfigurasi middleware global untuk aplikasi.
    ->withMiddleware(function (Middleware $middleware): void {
        // `$middleware->append(...)` menambahkan middleware kustom ke akhir tumpukan middleware global.
        // Ini berarti middleware `TrackVisitor` akan dijalankan untuk setiap permintaan HTTP
        // setelah middleware bawaan Laravel lainnya.
        $middleware->append(\App\Http\Middleware\TrackVisitor::class);
    })

    // Mengkonfigurasi penanganan exception global untuk aplikasi.
    ->withExceptions(function (Exceptions $exceptions): void {
        // Saat ini, tidak ada konfigurasi penanganan exception kustom yang didefinisikan di sini.
        // Penanganan exception default Laravel atau yang didefinisikan di
        // `App\Exceptions\Handler.php` akan digunakan.
        // Contoh: $exceptions->dontReport([SomeException::class]);
    })->create(); // Membuat dan mengembalikan instance aplikasi Laravel yang sudah terkonfigurasi.