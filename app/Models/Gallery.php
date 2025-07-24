<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;            // Mengimpor kelas dasar Model dari Eloquent.
use Illuminate\Database\Eloquent\Factories\HasFactory; // Mengimpor trait HasFactory untuk factory model (digunakan untuk seeding/testing).
use Illuminate\Database\Eloquent\SoftDeletes;      // Mengimpor trait SoftDeletes untuk fungsionalitas soft delete.

/**
 * Class Gallery
 *
 * Model ini merepresentasikan tabel 'galleries' di database.
 * Ini digunakan untuk mengelola data gambar atau item galeri,
 * termasuk kategori tempat gambar itu berada.
 */
class Gallery extends Model
{
    // Menggunakan trait-trait berikut untuk menambahkan fungsionalitas tertentu pada model:
    use HasFactory, SoftDeletes;

    /**
     * $fillable
     *
     * Properti ini mendefinisikan atribut-atribut (kolom) dalam tabel
     * yang dapat diisi secara massal (mass assignable).
     * Artinya, kolom-kolom ini dapat diatur melalui array atau objek pada saat
     * pembuatan atau pembaruan record secara massal.
     *
     * @var array
     */
    protected $fillable = ['gallery_category_id', 'name', 'description', 'image'];

    /**
     * category()
     *
     * Mendefinisikan relasi "belongsTo" dengan model GalleryCategory.
     * Ini berarti setiap item Gallery (gambar) adalah bagian dari (milik) satu GalleryCategory.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        // Parameter pertama: Nama kelas model yang menjadi tujuan relasi (GalleryCategory::class).
        // Parameter kedua: Nama kolom kunci asing di tabel 'galleries' yang menghubungkan
        //                  ke tabel 'gallery_categories' ('gallery_category_id').
        return $this->belongsTo(GalleryCategory::class, 'gallery_category_id');
    }
}
