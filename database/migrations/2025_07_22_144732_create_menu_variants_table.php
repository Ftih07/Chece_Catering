<?php

use Illuminate\Database\Migrations\Migration; // Mengimpor kelas dasar Migration.
use Illuminate\Database\Schema\Blueprint;    // Mengimpor kelas Blueprint untuk mendefinisikan struktur tabel.
use Illuminate\Support\Facades\Schema;       // Mengimpor Facade Schema untuk berinteraksi dengan skema database.

/**
 * File: database/migrations/YYYY_MM_DD_HHMMSS_create_menu_variants_table.php
 *
 * Migrasi ini bertanggung jawab untuk membuat dan menghapus tabel 'menu_variants' di database.
 * Tabel ini akan menyimpan berbagai varian atau opsi yang dapat dimiliki oleh setiap item menu,
 * seperti ukuran (kecil, sedang, besar), tingkat kepedasan, atau pilihan topping.
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
        // Membuat tabel baru dengan nama 'menu_variants'.
        Schema::create('menu_variants', function (Blueprint $table) {
            $table->id(); // Mendefinisikan kolom 'id' sebagai primary key auto-incrementing.

            // Mendefinisikan kolom foreign key 'menu_id'.
            // Ini menghubungkan varian menu ke item menu induknya.
            $table->foreignId('menu_id')
                ->constrained()      // Menetapkan batasan foreign key ke tabel 'menus' (secara implisit).
                ->cascadeOnDelete(); // Jika item menu induk dihapus, semua varian terkait juga akan dihapus.

            $table->string('name')->nullable(); // Mendefinisikan kolom 'name' sebagai VARCHAR.
            // `nullable()` berarti kolom ini bisa kosong.
            // Ini akan menyimpan nama varian (misal: "Large", "Pedas").

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
        // Menghapus tabel 'menu_variants' jika tabel tersebut ada.
        // Ini memastikan bahwa database kembali ke keadaan sebelum migrasi ini dijalankan.
        Schema::dropIfExists('menu_variants');
    }
};
