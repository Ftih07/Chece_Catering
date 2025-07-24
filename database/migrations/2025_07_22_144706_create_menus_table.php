<?php

use Illuminate\Database\Migrations\Migration; // Mengimpor kelas dasar Migration.
use Illuminate\Database\Schema\Blueprint;    // Mengimpor kelas Blueprint untuk mendefinisikan struktur tabel.
use Illuminate\Support\Facades\Schema;       // Mengimpor Facade Schema untuk berinteraksi dengan skema database.

/**
 * File: database/migrations/YYYY_MM_DD_HHMMSS_create_menus_table.php
 *
 * Migrasi ini bertanggung jawab untuk membuat dan menghapus tabel 'menus' di database.
 * Tabel ini akan menyimpan setiap item menu individual, seperti hidangan atau minuman.
 * Setiap item menu akan terkait dengan sebuah subkategori.
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
        // Membuat tabel baru dengan nama 'menus'.
        Schema::create('menus', function (Blueprint $table) {
            $table->id(); // Mendefinisikan kolom 'id' sebagai primary key auto-incrementing.

            // Mendefinisikan kolom foreign key 'menu_subcategory_id'.
            // Ini menghubungkan item menu ke subkategori yang menjadi bagiannya.
            $table->foreignId('menu_subcategory_id')
                ->constrained()      // Menetapkan batasan foreign key ke tabel 'menu_subcategories' (secara implisit).
                ->cascadeOnDelete(); // Jika subkategori induk dihapus, semua item menu terkait juga akan dihapus.

            $table->string('name'); // Mendefinisikan kolom 'name' sebagai VARCHAR.
            // Ini akan menyimpan nama item menu (misal: "Nasi Goreng Spesial", "Es Jeruk").

            $table->text('description')->nullable(); // Mendefinisikan kolom 'description' sebagai TEXT.
            // `nullable()` berarti kolom ini bisa kosong.
            // Ini akan menyimpan deskripsi detail tentang item menu.

            $table->decimal('price', 10, 2)->nullable(); // Mendefinisikan kolom 'price' sebagai DECIMAL.
            // 10: total digit (presisi), 2: digit setelah koma (skala).
            // `nullable()` berarti harga bisa kosong (misal: harga bervariasi).
            // Ini akan menyimpan harga item menu.

            $table->string('image')->nullable();    // Mendefinisikan kolom 'image' sebagai VARCHAR.
            // `nullable()` berarti kolom ini bisa kosong.
            // Ini biasanya menyimpan nama file atau path ke gambar item menu.

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
        // Menghapus tabel 'menus' jika tabel tersebut ada.
        // Ini memastikan bahwa database kembali ke keadaan sebelum migrasi ini dijalankan.
        Schema::dropIfExists('menus');
    }
};
