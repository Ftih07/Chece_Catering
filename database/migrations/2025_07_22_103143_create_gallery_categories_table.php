<?php

use Illuminate\Database\Migrations\Migration; // Mengimpor kelas dasar Migration.
use Illuminate\Database\Schema\Blueprint;    // Mengimpor kelas Blueprint untuk mendefinisikan struktur tabel.
use Illuminate\Support\Facades\Schema;       // Mengimpor Facade Schema untuk berinteraksi dengan skema database.

/**
 * File: database/migrations/YYYY_MM_DD_HHMMSS_create_gallery_categories_table.php
 *
 * Migrasi ini bertanggung jawab untuk membuat dan menghapus tabel 'gallery_categories'
 * di database. Tabel ini akan menyimpan informasi tentang kategori-kategori galeri.
 *
 * Migrasi adalah "version control" untuk database Anda, memungkinkan tim untuk
 * mendefinisikan dan berbagi skema database aplikasi.
 */
return new class extends Migration
{
    /**
     * up()
     *
     * Metode `up` dijalankan ketika migrasi dieksekusi (misalnya, dengan `php artisan migrate`).
     * Di sinilah logika untuk membuat tabel atau memodifikasi skema database didefinisikan.
     *
     * @return void
     */
    public function up(): void
    {
        // Membuat tabel baru dengan nama 'gallery_categories'.
        Schema::create('gallery_categories', function (Blueprint $table) {
            $table->id(); // Mendefinisikan kolom 'id' sebagai primary key auto-incrementing (BIGINT UNSIGNED AUTO_INCREMENT).

            $table->string('name'); // Mendefinisikan kolom 'name' sebagai VARCHAR.
            // Ini akan menyimpan nama kategori galeri (misal: "Pemandangan", "Hewan").

            $table->text('description')->nullable(); // Mendefinisikan kolom 'description' sebagai TEXT.
            // `nullable()` berarti kolom ini bisa kosong (tidak wajib diisi).
            // Ini akan menyimpan deskripsi singkat tentang kategori.

            $table->timestamps(); // Menambahkan dua kolom TIMESTAMP: `created_at` dan `updated_at`.
            // Laravel akan secara otomatis mengelola nilai kolom-kolom ini
            // saat record dibuat atau diperbarui.
        });
    }

    /**
     * down()
     *
     * Metode `down` dijalankan ketika migrasi di-rollback (misalnya, dengan `php artisan migrate:rollback`).
     * Di sinilah logika untuk membatalkan perubahan yang dilakukan oleh metode `up` didefinisikan.
     *
     * @return void
     */
    public function down(): void
    {
        // Menghapus tabel 'gallery_categories' jika tabel tersebut ada.
        // Ini memastikan bahwa database kembali ke keadaan sebelum migrasi ini dijalankan.
        Schema::dropIfExists('gallery_categories');
    }
};
