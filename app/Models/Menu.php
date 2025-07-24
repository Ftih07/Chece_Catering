<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // Mengimpor trait HasFactory untuk factory model.
use Illuminate\Database\Eloquent\Model;            // Mengimpor kelas dasar Model dari Eloquent.
use Illuminate\Database\Eloquent\SoftDeletes;      // Mengimpor trait SoftDeletes untuk fungsionalitas soft delete.

/**
 * Class Menu
 *
 * Model ini merepresentasikan tabel 'menus' di database.
 * Ini digunakan untuk mengelola data item menu individu,
 * termasuk informasi seperti nama, deskripsi, harga, gambar,
 * serta hubungannya dengan subkategori dan kategori menu.
 */
class Menu extends Model
{
    // Menggunakan trait-trait berikut untuk menambahkan fungsionalitas tertentu pada model:
    use HasFactory, SoftDeletes;

    /**
     * $fillable
     *
     * Properti ini mendefinisikan atribut-atribut (kolom) dalam tabel 'menus'
     * yang dapat diisi secara massal (mass assignable).
     *
     * @var array
     */
    protected $fillable = ['menu_subcategory_id', 'name', 'description', 'price', 'image'];

    /**
     * subcategory()
     *
     * Mendefinisikan relasi "belongsTo" dengan model MenuSubcategory.
     * Ini berarti setiap item Menu adalah bagian dari (milik) satu MenuSubcategory.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subcategory()
    {
        // Parameter kedua ('menu_subcategory_id') secara eksplisit menunjukkan
        // kolom foreign key di tabel 'menus' yang menghubungkan ke tabel 'menu_subcategories'.
        return $this->belongsTo(MenuSubcategory::class, 'menu_subcategory_id');
    }

    /**
     * category()
     *
     * Mendefinisikan relasi "hasOneThrough" dengan model MenuCategory.
     * Relasi ini digunakan ketika model saat ini (Menu) dapat mengakses
     * model terkait (MenuCategory) melalui model perantara (MenuSubcategory).
     *
     * Ini berarti dari sebuah item Menu, kita bisa langsung mendapatkan
     * kategori utamanya, melewati subkategorinya.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOneThrough
     */
    public function category()
    {
        return $this->hasOneThrough(
            MenuCategory::class,    // 1. Model tujuan akhir (yang ingin diakses dari Menu).
            MenuSubcategory::class, // 2. Model perantara (melalui mana MenuCategory diakses).
            'id',                   // 3. Foreign key di tabel *perantara* (MenuSubcategory) yang menunjuk ke *model ini* (Menu, di kolom 'menu_subcategory_id').
            //    Sebenarnya, ini adalah local key di model perantara yang digunakan untuk join.
            'id',                   // 4. Local key di tabel *tujuan akhir* (MenuCategory) yang ditunjuk oleh *model perantara*.
            //    Yaitu, ID dari MenuCategory.
            'menu_subcategory_id',  // 5. Local key di model *saat ini* (Menu) yang menunjuk ke *model perantara*.
            //    Ini adalah foreign key di tabel 'menus' untuk 'menu_subcategories'.
            'menu_category_id'      // 6. Local key di tabel *perantara* (MenuSubcategory) yang menunjuk ke *model tujuan akhir* (MenuCategory).
            //    Ini adalah foreign key di tabel 'menu_subcategories' untuk 'menu_categories'.
        );
    }

    /**
     * variants()
     *
     * Mendefinisikan relasi "hasMany" dengan model MenuVariant.
     * Ini berarti satu item Menu dapat memiliki banyak varian (misalnya, ukuran, pilihan topping).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function variants()
    {
        // Eloquent secara otomatis akan mengasumsikan foreign key adalah 'menu_id'
        // di tabel 'menu_variants' yang menunjuk kembali ke tabel 'menus'.
        return $this->hasMany(MenuVariant::class);
    }
}
