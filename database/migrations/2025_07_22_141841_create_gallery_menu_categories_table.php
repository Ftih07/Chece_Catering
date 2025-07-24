<?php

use Illuminate\Database\Migrations\Migration; // Mengimpor kelas dasar Migration.
use Illuminate\Database\Schema\Blueprint;    // Mengimpor kelas Blueprint untuk mendefinisikan struktur tabel.
use Illuminate\Support\Facades\Schema;       // Mengimpor Facade Schema untuk berinteraksi dengan skema database.

/**
 * File: database/migrations/YYYY_MM_DD_HHMMSS_create_gallery_menu_categories_table.php
 *
 * Migrasi ini bertanggung jawab untuk membuat dan menghapus tabel 'gallery_menu_categories'
 * di database. Tabel ini kemungkinan digunakan untuk menyimpan entri galeri
 * yang secara spesifik terkait dengan kategori menu.
 * Ini bisa berfungsi sebagai thumbnail atau gambar representatif untuk kategori menu.
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
        // Membuat tabel baru dengan nama 'gallery_menu_categories'.
        Schema::create('gallery_menu_categories', function (Blueprint $table) {
            $table->id(); // Mendefinisikan kolom 'id' sebagai primary key auto-incrementing.

            // Mendefinisikan kolom foreign key 'menu_category_id'.
            // Kolom ini akan menjadi kunci penghubung ke tabel 'menu_categories'.
            $table->foreignId('menu_category_id')
                ->constrained() // Menetapkan batasan foreign key ke tabel 'menu_categories' (secara implisit karena konvensi penamaan).
                ->cascadeOnDelete(); // Menentukan perilaku 'onDelete': jika kategori menu induk dihapus,
            // semua entri gallery_menu_categories yang terkait juga akan dihapus.

            $table->string('name'); // Mendefinisikan kolom 'name' sebagai VARCHAR.
            // Ini akan menyimpan nama atau judul untuk entri galeri kategori menu ini.

            $table->string('image'); // Mendefinisikan kolom 'image' sebagai VARCHAR.
            // Ini akan menyimpan nama file atau path ke gambar.

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
        // Menghapus tabel 'gallery_menu_categories' jika tabel tersebut ada.
        // Ini memastikan bahwa database kembali ke keadaan sebelum migrasi ini dijalankan.
        Schema::dropIfExists('gallery_menu_categories');
    }
};
