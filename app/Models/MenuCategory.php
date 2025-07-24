<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // Mengimpor trait HasFactory untuk factory model.
use Illuminate\Database\Eloquent\Model;            // Mengimpor kelas dasar Model dari Eloquent.
use Illuminate\Support\Str;                        // Mengimpor facade Str untuk fungsi manipulasi string (khususnya Str::slug).
use Illuminate\Database\Eloquent\SoftDeletes;      // Mengimpor trait SoftDeletes untuk fungsionalitas soft delete.

/**
 * Class MenuCategory
 *
 * Model ini merepresentasikan tabel 'menu_categories' di database.
 * Ini digunakan untuk mengelola kategori utama dari item-item menu,
 * seperti "Makanan Berat", "Minuman", "Camilan", dll.
 */
class MenuCategory extends Model
{
    // Menggunakan trait-trait berikut untuk menambahkan fungsionalitas tertentu pada model:
    use HasFactory, SoftDeletes;

    /**
     * $fillable
     *
     * Properti ini mendefinisikan atribut (kolom) dalam tabel 'menu_categories'
     * yang dapat diisi secara massal (mass assignable).
     *
     * @var array
     */
    protected $fillable = ['name']; // Hanya kolom 'name' yang dapat diisi secara massal.

    /**
     * galleries()
     *
     * Mendefinisikan relasi "hasMany" dengan model GalleryMenuCategory.
     * Ini berarti satu MenuCategory dapat memiliki banyak GalleryMenuCategory yang terkait.
     * Relasi ini mungkin digunakan untuk mengaitkan kategori menu dengan item galeri spesifik
     * yang mewakili kategori tersebut.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function galleries()
    {
        // Eloquent secara otomatis mengasumsikan foreign key 'menu_category_id'
        // di tabel 'gallery_menu_categories'.
        return $this->hasMany(GalleryMenuCategory::class);
    }

    /**
     * subcategories()
     *
     * Mendefinisikan relasi "hasMany" dengan model MenuSubcategory.
     * Ini berarti satu MenuCategory dapat memiliki banyak MenuSubcategory.
     * Contoh: Kategori "Makanan Berat" bisa memiliki subkategori "Nasi Goreng", "Mie Ayam", dll.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subcategories()
    {
        // Eloquent secara otomatis mengasumsikan foreign key 'menu_category_id'
        // di tabel 'menu_subcategories'.
        return $this->hasMany(MenuSubcategory::class);
    }

    /**
     * thumbnail()
     *
     * Mendefinisikan relasi "hasOne" dengan model GalleryMenuCategory.
     * Ini dirancang untuk mengambil *satu* gambar atau entri galeri terbaru
     * yang berfungsi sebagai thumbnail atau representasi visual untuk kategori menu ini.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function thumbnail()
    {
        // Menggunakan `latest()` untuk memastikan kita mengambil gambar yang paling baru
        // jika ada beberapa GalleryMenuCategory yang terkait.
        return $this->hasOne(GalleryMenuCategory::class)->latest();
    }

    /**
     * boot()
     *
     * Metode statis ini dipanggil secara otomatis saat model diinisialisasi.
     * Ini adalah tempat yang tepat untuk mendaftarkan "event listeners" (pengamat kejadian)
     * yang akan bereaksi terhadap kejadian-kejadian tertentu pada model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot(); // Pastikan metode boot dari parent class dipanggil terlebih dahulu.

        // Event listener 'saving':
        // Akan dieksekusi sebelum record kategori disimpan (baik saat membuat baru atau memperbarui).
        static::saving(function ($model) {
            // Memeriksa apakah kolom 'slug' belum diisi.
            if (!$model->slug) {
                // Jika kosong, secara otomatis menghasilkan slug dari nama kategori.
                // Ditambahkan `uniqid()` untuk memastikan slug unik jika ada nama kategori yang sama.
                $model->slug = Str::slug($model->name . '-' . uniqid());
            }
        });
    }
}
