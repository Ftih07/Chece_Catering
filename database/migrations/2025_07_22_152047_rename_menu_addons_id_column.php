<?php

use Illuminate\Database\Migrations\Migration; // Mengimpor kelas dasar Migration.
use Illuminate\Database\Schema\Blueprint;    // Mengimpor kelas Blueprint untuk mendefinisikan struktur tabel.
use Illuminate\Support\Facades\Schema;       // Mengimpor Facade Schema untuk berinteraksi dengan skema database.

/**
 * File: database/migrations/YYYY_MM_DD_HHMMSS_rename_menu_addons_id_to_menu_addon_id_in_menu_subcategories_table.php
 * (Nama file migrasi biasanya menyertakan tanggal dan deskripsi aksi)
 *
 * Migrasi ini bertanggung jawab untuk mengubah nama kolom 'menu_addons_id' menjadi 'menu_addon_id'
 * di tabel 'menu_subcategories'. Ini sering dilakukan untuk mengikuti konvensi penamaan Laravel
 * yang lebih konsisten untuk foreign key (`nama_model_singular_id`).
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
        // Menggunakan `Schema::table` untuk memodifikasi tabel yang sudah ada.
        // Dalam hal ini, tabel yang dimodifikasi adalah 'menu_subcategories'.
        Schema::table('menu_subcategories', function (Blueprint $table) {
            // Mengubah nama kolom dari 'menu_addons_id' menjadi 'menu_addon_id'.
            // Ini adalah perubahan penting untuk menjaga konsistensi penamaan foreign key
            // dengan konvensi Laravel (singular_model_id).
            $table->renameColumn('menu_addons_id', 'menu_addon_id');
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
        // Menggunakan `Schema::table` untuk memodifikasi tabel yang sudah ada saat rollback.
        Schema::table('menu_subcategories', function (Blueprint $table) {
            // Mengembalikan nama kolom dari 'menu_addon_id' menjadi 'menu_addons_id'.
            // Ini memastikan rollback yang bersih dan mengembalikan database ke keadaan sebelumnya.
            $table->renameColumn('menu_addon_id', 'menu_addons_id');
        });
    }
};
