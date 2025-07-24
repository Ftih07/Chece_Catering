<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // Mengimpor trait HasFactory untuk factory model.
use Illuminate\Database\Eloquent\Model;            // Mengimpor kelas dasar Model dari Eloquent.
use Illuminate\Support\Str;                        // Mengimpor facade Str untuk fungsi manipulasi string (khususnya Str::slug).
use Illuminate\Database\Eloquent\SoftDeletes;      // Mengimpor trait SoftDeletes untuk fungsionalitas soft delete.

/**
 * Class MenuSubcategory
 *
 * Model ini merepresentasikan tabel 'menu_subcategories' di database.
 * Ini digunakan untuk mengelola subkategori dari item-item menu,
 * seperti "Nasi Goreng" di bawah kategori "Makanan Berat", atau "Kopi" di bawah "Minuman".
 * Model ini juga dapat dikaitkan dengan addon dan memiliki path ke file PDF.
 */
class MenuSubcategory extends Model
{
    // Menggunakan trait-trait berikut untuk menambahkan fungsionalitas tertentu pada model:
    use HasFactory, SoftDeletes;

    /**
     * $fillable
     *
     * Properti ini mendefinisikan atribut-atribut (kolom) dalam tabel 'menu_subcategories'
     * yang dapat diisi secara massal (mass assignable).
     *
     * @var array
     */
    protected $fillable = [
        'menu_category_id', // Kunci asing yang menghubungkan subkategori ini ke kategori menu utamanya.
        'menu_addon_id',    // Kunci asing yang menghubungkan subkategori ini ke addon terkait (jika ada).
        'name',             // Nama subkategori (misal: "Nasi Goreng", "Es Kopi").
        'pdf_path'          // Path atau URL ke file PDF yang terkait dengan subkategori ini (opsional).
    ];

    /**
     * category()
     *
     * Mendefinisikan relasi "belongsTo" dengan model MenuCategory.
     * Ini berarti setiap MenuSubcategory adalah bagian dari (milik) satu MenuCategory.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        // Parameter kedua ('menu_category_id') secara eksplisit menunjukkan
        // kolom foreign key di tabel 'menu_subcategories' yang menghubungkan
        // ke tabel 'menu_categories'.
        return $this->belongsTo(MenuCategory::class, 'menu_category_id');
    }

    /**
     * addon()
     *
     * Mendefinisikan relasi "belongsTo" dengan model MenuAddon.
     * Ini berarti setiap MenuSubcategory dapat terkait dengan (milik) satu MenuAddon.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function addon()
    {
        // Parameter kedua ('menu_addon_id') secara eksplisit menunjukkan
        // kolom foreign key di tabel 'menu_subcategories' yang menghubungkan
        // ke tabel 'menu_addons'.
        return $this->belongsTo(MenuAddon::class, 'menu_addon_id');
    }

    /**
     * menus()
     *
     * Mendefinisikan relasi "hasMany" dengan model Menu.
     * Ini berarti satu MenuSubcategory dapat memiliki banyak item Menu.
     * Contoh: Subkategori "Nasi Goreng" bisa memiliki item menu "Nasi Goreng Biasa",
     * "Nasi Goreng Seafood", dll.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function menus()
    {
        // Eloquent secara otomatis akan mengasumsikan foreign key adalah 'menu_subcategory_id'
        // di tabel 'menus' yang menunjuk kembali ke tabel 'menu_subcategories'.
        return $this->hasMany(Menu::class);
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
        // Akan dieksekusi sebelum record subkategori disimpan (baik saat membuat baru atau memperbarui).
        static::saving(function ($model) {
            // Memeriksa apakah kolom 'slug' belum diisi.
            // Kolom 'slug' tidak ada di $fillable, jadi ini mungkin kolom tersembunyi
            // atau diisi secara otomatis.
            if (!$model->slug) {
                // Jika kosong, secara otomatis menghasilkan slug dari nama subkategori.
                // Ditambahkan `uniqid()` untuk memastikan slug unik jika ada nama subkategori yang sama.
                $model->slug = Str::slug($model->name . '-' . uniqid());
            }
        });
    }
}
