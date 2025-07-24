<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;            // Mengimpor kelas dasar Model dari Eloquent.
use Illuminate\Database\Eloquent\Factories\HasFactory; // Mengimpor trait HasFactory untuk factory model.
use Illuminate\Database\Eloquent\SoftDeletes;      // Mengimpor trait SoftDeletes untuk fungsionalitas soft delete.

use Illuminate\Support\Str; // Mengimpor facade Str untuk fungsi manipulasi string (khususnya Str::slug).

/**
 * Class GalleryCategory
 *
 * Model ini merepresentasikan tabel 'gallery_categories' di database.
 * Ini digunakan untuk mengelola data kategori galeri,
 * yang akan mengelompokkan berbagai item galeri.
 */
class GalleryCategory extends Model
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
    protected $fillable = ['name', 'description', 'slug'];

    /**
     * boot()
     *
     * Metode statis ini dipanggil secara otomatis saat model diinisialisasi.
     * Ini adalah tempat yang tepat untuk mendaftarkan "event listeners" (pengamat kejadian)
     * yang akan bereaksi terhadap kejadian-kejadian tertentu pada model (misalnya, saat dibuat atau diperbarui).
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot(); // Pastikan metode boot dari parent class dipanggil terlebih dahulu.

        // Event listener 'creating':
        // Akan dieksekusi sebelum record kategori baru dibuat di database.
        static::creating(function ($category) {
            // Mengubah 'name' kategori menjadi 'slug' yang ramah URL.
            // Contoh: "Kategori Baru Saya" akan menjadi "kategori-baru-saya".
            $category->slug = Str::slug($category->name);
        });

        // Event listener 'updating':
        // Akan dieksekusi sebelum record kategori yang sudah ada diperbarui di database.
        static::updating(function ($category) {
            // Sama seperti 'creating', ini memastikan slug diperbarui setiap kali nama diubah.
            $category->slug = Str::slug($category->name);
        });
    }

    /**
     * galleries()
     *
     * Mendefinisikan relasi "hasMany" dengan model Gallery.
     * Ini berarti satu GalleryCategory dapat memiliki banyak item Gallery (gambar).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function galleries()
    {
        // Parameter pertama: Nama kelas model yang menjadi tujuan relasi (Gallery::class).
        // Eloquent secara otomatis akan mengasumsikan foreign key adalah 'gallery_category_id'
        // di tabel 'galleries' karena konvensi penamaan.
        return $this->hasMany(Gallery::class);
    }
}
