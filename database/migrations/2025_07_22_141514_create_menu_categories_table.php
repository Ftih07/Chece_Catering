<?php

use Illuminate\Database\Migrations\Migration; // Mengimpor kelas dasar Migration.
use Illuminate\Database\Schema\Blueprint;    // Mengimpor kelas Blueprint untuk mendefinisikan struktur tabel.
use Illuminate\Support\Facades\Schema;       // Mengimpor Facade Schema untuk berinteraksi dengan skema database.

/**
 * File: database/migrations/YYYY_MM_DD_HHMMSS_create_menu_categories_table.php
 *
 * Migrasi ini bertanggung jawab untuk membuat dan menghapus tabel 'menu_categories'
 * di database. Tabel ini akan menyimpan kategori-kategori utama untuk menu.
 *
 * Migrasi adalah "version control" untuk database Anda, memungkinkan tim untuk
 * mendefinisikan dan berbagi skema database aplikasi secara terstruktur.
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
        // Membuat tabel baru dengan nama 'menu_categories'.
        Schema::create('menu_categories', function (Blueprint $table) {
            $table->id(); // Mendefinisikan kolom 'id' sebagai primary key auto-incrementing (BIGINT UNSIGNED AUTO_INCREMENT).
            // Ini akan menjadi pengenal unik untuk setiap kategori menu.

            $table->string('name'); // Mendefinisikan kolom 'name' sebagai VARCHAR.
            // Ini akan menyimpan nama kategori menu (misal: "Makanan Berat", "Minuman").

            $table->timestamps(); // Menambahkan dua kolom TIMESTAMP: `created_at` dan `updated_at`.
            // Laravel akan secara otomatis mengelola nilai kolom-kolom ini
            // saat record dibuat atau diperbarui, berguna untuk pelacakan waktu.
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
        // Menghapus tabel 'menu_categories' jika tabel tersebut ada.
        // Ini memastikan bahwa database kembali ke keadaan sebelum migrasi ini dijalankan.
        Schema::dropIfExists('menu_categories');
    }
};
