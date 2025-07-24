<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // Mengimpor trait HasFactory untuk factory model.
use Illuminate\Database\Eloquent\Model;            // Mengimpor kelas dasar Model dari Eloquent.
use Illuminate\Database\Eloquent\SoftDeletes;      // Mengimpor trait SoftDeletes untuk fungsionalitas soft delete.

/**
 * Class MenuAddon
 *
 * Model ini merepresentasikan tabel 'menu_addons' di database.
 * Ini digunakan untuk mengelola data "addon" atau informasi tambahan
 * yang mungkin terkait dengan satu atau lebih subkategori menu.
 * Contoh: Informasi alergen, panduan porsi, atau catatan khusus.
 */
class MenuAddon extends Model
{
    // Menggunakan trait-trait berikut untuk menambahkan fungsionalitas tertentu pada model:
    use HasFactory, SoftDeletes;

    /**
     * $fillable
     *
     * Properti ini mendefinisikan atribut-atribut (kolom) dalam tabel 'menu_addons'
     * yang dapat diisi secara massal (mass assignable).
     *
     * @var array
     */
    protected $fillable = ['title', 'description'];

    /**
     * subcategories()
     *
     * Mendefinisikan relasi "hasMany" dengan model MenuSubcategory.
     * Ini berarti satu MenuAddon dapat terkait dengan banyak MenuSubcategory.
     *
     * Relasi ini mengasumsikan bahwa tabel `menu_subcategories` memiliki
     * foreign key `menu_addon_id` yang menunjuk kembali ke tabel `menu_addons`.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subcategories()
    {
        // Eloquent secara otomatis akan mengasumsikan foreign key adalah 'menu_addon_id'
        // di tabel 'menu_subcategories' yang menunjuk kembali ke tabel 'menu_addons'.
        return $this->hasMany(MenuSubcategory::class);
    }
}
