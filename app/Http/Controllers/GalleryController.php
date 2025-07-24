<?php

namespace App\Http\Controllers;

use App\Models\Gallery; // Mengimpor model Gallery untuk berinteraksi dengan data galeri.
use App\Models\GalleryCategory; // Mengimpor model GalleryCategory untuk berinteraksi dengan kategori galeri.
use Illuminate\Http\Request; // Mengimpor kelas Request untuk menangani input dari HTTP request.

/**
 * Class GalleryController
 *
 * Controller ini bertanggung jawab untuk mengelola logika terkait
 * tampilan galeri di sisi publik (frontend) aplikasi.
 * Ini menangani pengambilan data galeri dan kategorinya, serta
 * pemfilteran galeri berdasarkan kategori yang dipilih.
 */
class GalleryController extends Controller
{
    /**
     * index()
     *
     * Metode ini menampilkan halaman utama galeri.
     * Ia mengambil semua kategori galeri dan data galeri,
     * dengan opsi untuk memfilter galeri berdasarkan kategori yang dipilih
     * melalui parameter URL.
     *
     * @param  \Illuminate\Http\Request  $request Objek request HTTP yang berisi parameter query.
     * @return \Illuminate\View\View Mengembalikan view 'gallery.index' dengan data yang diperlukan.
     */
    public function index(Request $request)
    {
        // Mengambil semua kategori galeri dari database.
        // Data ini biasanya digunakan untuk menampilkan daftar filter kategori di UI.
        $categories = GalleryCategory::all();

        // Mengambil nilai parameter 'category' dari URL query string (misal: ?category=slug-kategori).
        // Jika parameter tidak ada, $selectedSlug akan null.
        $selectedSlug = $request->get('category');

        // Inisialisasi variabel untuk menyimpan objek kategori yang dipilih.
        $selectedCategory = null;
        // Memeriksa apakah ada slug kategori yang dipilih dari URL.
        if ($selectedSlug) {
            // Jika ada, cari kategori galeri berdasarkan slug tersebut.
            // `first()` akan mengembalikan objek kategori pertama yang cocok atau null jika tidak ditemukan.
            $selectedCategory = GalleryCategory::where('slug', $selectedSlug)->first();
        }

        // Mengambil data galeri dari database.
        // Metode `when()` digunakan untuk menerapkan kondisi query secara kondisional.
        $galleries = Gallery::when($selectedCategory, function ($query) use ($selectedCategory) {
            // Jika $selectedCategory tidak null (artinya ada kategori yang dipilih),
            // tambahkan kondisi WHERE untuk memfilter galeri berdasarkan 'gallery_category_id'
            // yang cocok dengan ID kategori yang dipilih.
            $query->where('gallery_category_id', $selectedCategory->id);
        })->get(); // Mengeksekusi query dan mendapatkan semua hasil.

        // Mengembalikan view 'gallery.index' (yang terletak di resources/views/gallery/index.blade.php).
        // Fungsi `compact()` digunakan untuk meneruskan variabel-variabel PHP ke view.
        // Variabel yang diteruskan:
        // - 'categories': Semua kategori galeri.
        // - 'galleries': Data galeri yang sudah difilter (atau semua jika tidak ada filter).
        // - 'selectedCategory': Objek kategori yang sedang aktif/dipilih (atau null).
        // - 'selectedSlug': Slug kategori yang dipilih dari URL (atau null).
        return view('gallery.index', compact('categories', 'galleries', 'selectedCategory', 'selectedSlug'));
    }
}
