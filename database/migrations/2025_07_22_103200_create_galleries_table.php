<?php

use Illuminate\Database\Migrations\Migration; // Mengimpor kelas dasar Migration.
use Illuminate\Database\Schema\Blueprint;    // Mengimpor kelas Blueprint untuk mendefinisikan struktur tabel.
use Illuminate\Support\Facades\Schema;       // Mengimpor Facade Schema untuk berinteraksi dengan skema database.

/**
 * File: database/migrations/YYYY_MM_DD_HHMMSS_create_galleries_table.php
 *
 * Migrasi ini bertanggung jawab untuk membuat dan menghapus tabel 'galleries'
 * di database. Tabel ini akan menyimpan informasi tentang setiap item galeri (gambar)
 * dan mengaitkannya dengan kategori galeri yang sudah ada.
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
        // Membuat tabel baru dengan nama 'galleries'.
        Schema::create('galleries', function (Blueprint $table) {
            $table->id(); // Mendefinisikan kolom 'id' sebagai primary key auto-incrementing (BIGINT UNSIGNED AUTO_INCREMENT).

            // Mendefinisikan kolom foreign key 'gallery_category_id'.
            // Kolom ini akan menjadi kunci penghubung ke tabel 'gallery_categories'.
            $table->foreignId('gallery_category_id')
                ->constrained('gallery_categories') // Menetapkan batasan foreign key ke tabel 'gallery_categories'.
                ->onDelete('cascade');              // Menentukan perilaku 'onDelete': jika kategori induk dihapus,
            // semua item galeri yang terkait dengan kategori tersebut juga akan dihapus.

            $table->string('name'); // Mendefinisikan kolom 'name' sebagai VARCHAR.
            // Ini akan menyimpan nama atau judul item galeri.

            $table->text('description')->nullable(); // Mendefinisikan kolom 'description' sebagai TEXT.
            // `nullable()` berarti kolom ini bisa kosong.
            // Ini akan menyimpan deskripsi detail tentang gambar galeri.

            $table->string('image')->nullable();    // Mendefinisikan kolom 'image' sebagai VARCHAR.
            // `nullable()` berarti kolom ini bisa kosong.
            // Ini biasanya menyimpan nama file atau path ke gambar galeri.

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
        // Menghapus tabel 'galleries' jika tabel tersebut ada.
        // Ini memastikan bahwa database kembali ke keadaan sebelum migrasi ini dijalankan.
        Schema::dropIfExists('galleries');
    }
};
