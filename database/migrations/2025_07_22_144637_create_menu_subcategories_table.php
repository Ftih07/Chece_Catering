<?php

use Illuminate\Database\Migrations\Migration; // Mengimpor kelas dasar Migration.
use Illuminate\Database\Schema\Blueprint;    // Mengimpor kelas Blueprint untuk mendefinisikan struktur tabel.
use Illuminate\Support\Facades\Schema;       // Mengimpor Facade Schema untuk berinteraksi dengan skema database.

/**
 * File: database/migrations/YYYY_MM_DD_HHMMSS_create_menu_subcategories_table.php
 *
 * Migrasi ini bertanggung jawab untuk membuat dan menghapus tabel 'menu_subcategories'
 * di database. Tabel ini akan menyimpan subkategori untuk item-item menu,
 * membentuk hierarki di bawah kategori menu utama.
 * Ini juga dapat mengaitkan subkategori dengan addon dan file PDF.
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
        // Membuat tabel baru dengan nama 'menu_subcategories'.
        Schema::create('menu_subcategories', function (Blueprint $table) {
            $table->id(); // Mendefinisikan kolom 'id' sebagai primary key auto-incrementing.

            // Mendefinisikan kolom foreign key 'menu_category_id'.
            // Ini menghubungkan subkategori ke kategori menu utamanya.
            $table->foreignId('menu_category_id')
                ->constrained()      // Menetapkan batasan foreign key ke tabel 'menu_categories' (secara implisit).
                ->cascadeOnDelete(); // Jika kategori menu induk dihapus, semua subkategori terkait juga akan dihapus.

            // Mendefinisikan kolom foreign key 'menu_addons_id'.
            // Kolom ini menghubungkan subkategori ke addon terkait (misal: info alergen).
            $table->foreignId('menu_addons_id')
                ->nullable()         // `nullable()` berarti kolom ini bisa kosong (subkategori tidak harus punya addon).
                ->constrained('menu_addons') // Menetapkan batasan foreign key ke tabel 'menu_addons'.
                ->nullOnDelete();    // Jika addon induk dihapus, nilai 'menu_addons_id' di subkategori akan diatur menjadi NULL.
            // Ini berbeda dengan `cascadeOnDelete` yang akan menghapus seluruh baris.

            $table->string('name'); // Mendefinisikan kolom 'name' sebagai VARCHAR.
            // Ini akan menyimpan nama subkategori (misal: "Nasi Goreng", "Kopi").

            $table->string('pdf_path')->nullable(); // Mendefinisikan kolom 'pdf_path' sebagai VARCHAR.
            // `nullable()` berarti kolom ini bisa kosong.
            // Ini dapat menyimpan path ke file PDF terkait subkategori.

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
        // Menghapus tabel 'menu_subcategories' jika tabel tersebut ada.
        // Ini memastikan bahwa database kembali ke keadaan sebelum migrasi ini dijalankan.
        Schema::dropIfExists('menu_subcategories');
    }
};
