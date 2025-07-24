<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // Mengimpor trait HasFactory untuk factory model.
use Illuminate\Database\Eloquent\Model;            // Mengimpor kelas dasar Model dari Eloquent.
use Illuminate\Database\Eloquent\SoftDeletes;      // Mengimpor trait SoftDeletes untuk fungsionalitas soft delete.

/**
 * Class MenuVariant
 *
 * Model ini merepresentasikan tabel 'menu_variants' di database.
 * Ini digunakan untuk mengelola berbagai varian atau opsi untuk setiap item menu,
 * seperti ukuran (kecil, sedang, besar), tingkat kepedasan, atau pilihan topping.
 */
class MenuVariant extends Model
{
    // Menggunakan trait-trait berikut untuk menambahkan fungsionalitas tertentu pada model:
    use HasFactory, SoftDeletes;

    /**
     * $fillable
     *
     * Properti ini mendefinisikan atribut-atribut (kolom) dalam tabel 'menu_variants'
     * yang dapat diisi secara massal (mass assignable).
     *
     * @var array
     */
    protected $fillable = ['menu_id', 'name']; // Kolom yang dapat diisi secara massal.

    /**
     * menu()
     *
     * Mendefinisikan relasi "belongsTo" dengan model Menu.
     * Ini berarti setiap MenuVariant adalah bagian dari (milik) satu item Menu.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function menu()
    {
        // Eloquent secara otomatis akan mengasumsikan foreign key adalah 'menu_id'
        // di tabel 'menu_variants' yang menunjuk ke tabel 'menus'.
        return $this->belongsTo(Menu::class);
    }
}
