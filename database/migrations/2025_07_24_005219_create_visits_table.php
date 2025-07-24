<?php

use Illuminate\Database\Migrations\Migration; // Mengimpor kelas dasar Migration.
use Illuminate\Database\Schema\Blueprint;    // Mengimpor kelas Blueprint untuk mendefinisikan struktur tabel.
use Illuminate\Support\Facades\Schema;       // Mengimpor Facade Schema untuk berinteraksi dengan skema database.

/**
 * File: database/migrations/YYYY_MM_DD_HHMMSS_create_visits_table.php
 *
 * Migrasi ini bertanggung jawab untuk membuat dan menghapus tabel 'visits' di database.
 * Tabel ini dirancang untuk mencatat setiap kunjungan ke situs web,
 * mengumpulkan data penting tentang pengunjung dan halaman yang mereka akses.
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
        // Membuat tabel baru dengan nama 'visits'.
        Schema::create('visits', function (Blueprint $table) {
            $table->id(); // Mendefinisikan kolom 'id' sebagai primary key auto-incrementing.

            $table->string('ip_address')->nullable(); // Mendefinisikan kolom 'ip_address' sebagai VARCHAR.
            // `nullable()` berarti kolom ini bisa kosong.
            // Ini akan menyimpan alamat IP pengunjung.

            $table->string('user_agent')->nullable(); // Mendefinisikan kolom 'user_agent' sebagai VARCHAR.
            // `nullable()` berarti kolom ini bisa kosong.
            // Ini akan menyimpan string User-Agent (info browser/OS) pengunjung.

            $table->text('url')->nullable();          // Mendefinisikan kolom 'url' sebagai TEXT.
            // `nullable()` berarti kolom ini bisa kosong.
            // Ini akan menyimpan URL lengkap yang dikunjungi.

            $table->text('referrer')->nullable();     // Mendefinisikan kolom 'referrer' sebagai TEXT.
            // `nullable()` berarti kolom ini bisa kosong.
            // Ini akan menyimpan URL dari mana pengunjung datang (jika ada).

            $table->string('path')->nullable();       // Mendefinisikan kolom 'path' sebagai VARCHAR.
            // `nullable()` berarti kolom ini bisa kosong.
            // Ini akan menyimpan jalur relatif URL (misal: /menu, /gallery).

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
        // Menghapus tabel 'visits' jika tabel tersebut ada.
        // Ini memastikan bahwa database kembali ke keadaan sebelum migrasi ini dijalankan.
        Schema::dropIfExists('visits');
    }
};
