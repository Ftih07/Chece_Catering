<?php

namespace App\Http\Controllers;

use App\Models\GalleryCategory; // Mengimpor model GalleryCategory untuk berinteraksi dengan kategori galeri.
use App\Models\MenuCategory;    // Mengimpor model MenuCategory untuk berinteraksi dengan kategori menu.
use Illuminate\Http\Request;     // Mengimpor kelas Request, meskipun tidak digunakan secara langsung di metode index ini,
// ini adalah praktik umum untuk controller.

/**
 * Class HomeController
 *
 * Controller ini bertanggung jawab untuk mengelola logika terkait
 * tampilan halaman beranda (homepage) aplikasi.
 * Ini mengambil data yang relevan seperti kategori menu dan kategori galeri
 * untuk ditampilkan di halaman utama.
 */
class HomeController extends Controller
{
    /**
     * index()
     *
     * Metode ini menampilkan halaman beranda aplikasi.
     * Ia mengambil data kategori menu dan kategori galeri,
     * yang kemudian akan digunakan untuk mengisi konten dinamis di homepage.
     *
     * @return \Illuminate\View\View Mengembalikan view 'home' dengan data yang diperlukan.
     */
    public function index()
    {
        // Mengambil semua kategori menu dari database.
        // `with('thumbnail')` adalah eager loading. Ini berarti Laravel akan mengambil
        // data thumbnail yang terkait dengan setiap kategori menu dalam satu query tambahan,
        // mencegah masalah N+1 query dan meningkatkan performa.
        $menuCategories = MenuCategory::with('thumbnail')->get();

        // Mengambil semua kategori galeri dari database.
        // `with(['galleries' => function ($query) { ... }])` adalah eager loading dengan kondisi.
        // Ini akan mengambil data galeri yang terkait dengan setiap kategori galeri.
        // `function ($query) { $query->limit(1); }` membatasi hanya 1 gambar galeri pertama
        // untuk setiap kategori. Ini mungkin digunakan untuk menampilkan preview atau
        // gambar sampul untuk setiap kategori galeri di halaman beranda.
        $galleryCategories = GalleryCategory::with(['galleries' => function ($query) {
            $query->limit(1);
        }])->get();

        // Mengembalikan view 'home' (yang terletak di resources/views/home.blade.php).
        // Fungsi `compact()` digunakan untuk meneruskan variabel-variabel PHP ke view.
        // Variabel yang diteruskan:
        // - 'menuCategories': Koleksi semua kategori menu beserta thumbnailnya.
        // - 'galleryCategories': Koleksi semua kategori galeri beserta satu gambar galerinya.
        return view('home', compact('menuCategories', 'galleryCategories'));
    }
}
