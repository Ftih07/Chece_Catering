<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // Mengimpor trait HasFactory untuk factory model.
use Illuminate\Database\Eloquent\Model;            // Mengimpor kelas dasar Model dari Eloquent.
use Illuminate\Database\Eloquent\SoftDeletes;      // Mengimpor trait SoftDeletes untuk fungsionalitas soft delete.

/**
 * Class GalleryMenuCategory
 *
 * Model ini merepresentasikan tabel 'gallery_menu_categories' di database.
 * Nama model ini mungkin menunjukkan adanya hubungan atau representasi visual
 * dari kategori menu dalam konteks galeri, atau mungkin sebagai kategori khusus
 * untuk galeri yang terkait dengan menu.
 */
class GalleryMenuCategory extends Model
{
    // Menggunakan trait-trait berikut untuk menambahkan fungsionalitas tertentu pada model:
    use HasFactory, SoftDeletes;

    /**
     * $fillable
     *
     * Properti ini mendefinisikan atribut-atribut (kolom) dalam tabel
     * yang dapat diisi secara massal (mass assignable).
     *
     * @var array
     */
    protected $fillable = ['menu_category_id', 'name', 'image'];

    /**
     * category()
     *
     * Mendefinisikan relasi "belongsTo" dengan model MenuCategory.
     * Ini berarti setiap item GalleryMenuCategory adalah bagian dari (milik) satu MenuCategory.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        // Parameter pertama: Nama kelas model yang menjadi tujuan relasi (MenuCategory::class).
        // Eloquent secara otomatis akan mengasumsikan foreign key adalah 'menu_category_id'
        // di tabel 'gallery_menu_categories' karena konvensi penamaan.
        return $this->belongsTo(MenuCategory::class);
    }
}
