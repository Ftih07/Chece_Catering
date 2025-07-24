<?php

use Illuminate\Database\Migrations\Migration; // Mengimpor kelas dasar Migration.
use Illuminate\Database\Schema\Blueprint;    // Mengimpor kelas Blueprint untuk mendefinisikan struktur tabel.
use Illuminate\Support\Facades\Schema;       // Mengimpor Facade Schema untuk berinteraksi dengan skema database.

/**
 * File: database/migrations/YYYY_MM_DD_HHMMSS_add_slug_to_menu_categories_and_subcategories_table.php
 * (Nama file migrasi yang disarankan)
 *
 * Migrasi ini bertanggung jawab untuk menambahkan kolom 'slug' ke tabel
 * 'menu_categories' dan 'menu_subcategories'. Kolom slug digunakan
 * untuk URL yang ramah SEO dan unik untuk setiap entri.
 */
return new class extends Migration
{
    /**
     * up()
     *
     * Metode `up` dijalankan ketika migrasi dieksekusi (misalnya, dengan `php artisan migrate`).
     * Di sinilah logika untuk memodifikasi skema database didefinisikan.
     *
     * @return void
     */
    public function up(): void
    {
        // Memodifikasi tabel 'menu_categories' yang sudah ada.
        Schema::table('menu_categories', function (Blueprint $table) {
            // Menambahkan kolom 'slug' dengan tipe data string (VARCHAR).
            // `unique()` memastikan setiap slug adalah unik di tabel ini.
            // `nullable()` memungkinkan slug untuk sementara kosong saat entri dibuat
            // (karena kemungkinan diisi otomatis oleh event model).
            $table->string('slug')->unique()->nullable();
        });

        // Memodifikasi tabel 'menu_subcategories' yang sudah ada.
        Schema::table('menu_subcategories', function (Blueprint $table) {
            // Menambahkan kolom 'slug' dengan tipe data string (VARCHAR).
            // `unique()` memastikan setiap slug adalah unik di tabel ini.
            // `nullable()` memungkinkan slug untuk sementara kosong.
            $table->string('slug')->unique()->nullable();
        });
    }

    /**
     * down()
     *
     * Metode `down` dijalankan ketika migrasi di-rollback (misalnya, dengan `php artisan migrate:rollback`).
     * Di sinilah logika untuk membatalkan perubahan yang dilakukan oleh metode `up` didefinisikan.
     *
     * Catatan: Ada potensi masalah dalam metode `down` ini karena mencoba
     * memodifikasi tabel 'menu_categories_and_subcategories' yang kemungkinan tidak ada
     * atau bukan nama tabel yang dimaksud. Seharusnya membatalkan penambahan kolom
     * di tabel 'menu_categories' dan 'menu_subcategories' secara terpisah.
     * Implementasi yang benar ada di bagian Penjelasan di bawah.
     *
     * @return void
     */
    public function down(): void
    {
        // Saat ini, ada kesalahan dalam nama tabel yang ditargetkan untuk rollback.
        // Seharusnya menghapus kolom 'slug' dari 'menu_categories' dan 'menu_subcategories'.
        // Contoh kode yang benar untuk `down` ada di bagian penjelasan.

        // Perbaikan yang seharusnya ada di sini:
        Schema::table('menu_categories', function (Blueprint $table) {
            $table->dropColumn('slug');
        });

        Schema::table('menu_subcategories', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
