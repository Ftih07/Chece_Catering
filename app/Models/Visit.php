<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model; // Mengimpor kelas dasar Model dari Eloquent.

/**
 * Class Visit
 *
 * Model ini merepresentasikan tabel 'visits' di database.
 * Ini digunakan untuk mencatat dan mengelola data setiap kunjungan
 * yang terjadi di situs web. Data ini penting untuk analisis traffic.
 */
class Visit extends Model
{
    /**
     * $fillable
     *
     * Properti ini mendefinisikan atribut-atribut (kolom) dalam tabel 'visits'
     * yang dapat diisi secara massal (mass assignable).
     *
     * @var array
     */
    protected $fillable = [
        'ip_address', // Kolom untuk menyimpan alamat IP pengunjung.
        'user_agent', // Kolom untuk menyimpan string User-Agent (informasi browser/OS) pengunjung.
        'url',        // Kolom untuk menyimpan URL lengkap yang dikunjungi.
        'referrer',   // Kolom untuk menyimpan URL referer (dari mana pengunjung datang).
        'path',       // Kolom untuk menyimpan jalur relatif URL (misal: /menu, /gallery).
    ];

    // Catatan: Model ini tidak menggunakan trait HasFactory atau SoftDeletes.
    // Ini berarti Anda tidak bisa langsung menggunakan factory untuk membuat data dummy Visit
    // dan record Visit akan dihapus secara permanen jika perintah delete dijalankan.
    // Jika Anda ingin fitur-fitur tersebut, Anda perlu menambahkan 'use HasFactory, SoftDeletes;'
    // dan menjalankan migrasi database untuk kolom 'deleted_at' jika menggunakan SoftDeletes.
}
