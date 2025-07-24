<?php

use Illuminate\Database\Migrations\Migration; // Mengimpor kelas dasar Migration.
use Illuminate\Database\Schema\Blueprint;    // Mengimpor kelas Blueprint untuk mendefinisikan struktur tabel.
use Illuminate\Support\Facades\Schema;       // Mengimpor Facade Schema untuk berinteraksi dengan skema database.

/**
 * File: database/migrations/YYYY_MM_DD_HHMMSS_create_menu_addons_table.php
 *
 * Migrasi ini bertanggung jawab untuk membuat dan menghapus tabel 'menu_addons'
 * di database. Tabel ini akan menyimpan informasi tentang "addon" atau catatan tambahan
 * yang dapat dikaitkan dengan item atau subkategori menu.
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
        // Membuat tabel baru dengan nama 'menu_addons'.
        Schema::create('menu_addons', function (Blueprint $table) {
            $table->id(); // Mendefinisikan kolom 'id' sebagai primary key auto-incrementing.

            $table->string('title'); // Mendefinisikan kolom 'title' sebagai VARCHAR.
            // Ini akan menyimpan judul singkat untuk addon (misal: "Informasi Alergen").

            $table->text('description')->nullable(); // Mendefinisikan kolom 'description' sebagai TEXT.
            // `nullable()` berarti kolom ini bisa kosong.
            // Ini akan menyimpan deskripsi detail atau isi dari addon.

            $table->timestamps(); // Menambahkan dua kolom TIMESTAMP: `created_at` dan `updated_at`.
            // Laravel akan secara otomatis mengelola nilai kolom-kolom ini.
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
        // Menghapus tabel 'menu_addons' jika tabel tersebut ada.
        // Ini memastikan bahwa database kembali ke keadaan sebelum migrasi ini dijalankan.
        Schema::dropIfExists('menu_addons');
    }
};
