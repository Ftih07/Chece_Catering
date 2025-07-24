<?php

namespace App\Http\Controllers;

use App\Models\MenuCategory;     // Mengimpor model MenuCategory untuk berinteraksi dengan kategori menu.
use App\Models\Menu;            // Mengimpor model Menu untuk berinteraksi dengan item menu.
use App\Models\MenuAddon;       // Mengimpor model MenuAddon, meskipun tidak digunakan secara langsung di sini.
use App\Models\MenuSubcategory; // Mengimpor model MenuSubcategory untuk berinteraksi dengan subkategori menu.
use Illuminate\Http\Request;     // Mengimpor kelas Request untuk menangani input dari HTTP request.

/**
 * Class MenuController
 *
 * Controller ini bertanggung jawab untuk mengelola logika terkait
 * tampilan daftar menu di sisi publik (frontend) aplikasi.
 * Ini menangani pengambilan dan pemfilteran menu berdasarkan kategori dan subkategori
 * yang dipilih oleh pengguna.
 */
class MenuController extends Controller
{
    /**
     * index()
     *
     * Metode ini menampilkan halaman utama daftar menu.
     * Ia mengambil kategori dan subkategori menu, serta item menu itu sendiri,
     * dengan opsi untuk memfilter menu berdasarkan kategori atau subkategori yang dipilih
     * melalui parameter URL.
     *
     * @param  \Illuminate\Http\Request  $request Objek request HTTP yang berisi parameter query.
     * @return \Illuminate\View\View Mengembalikan view 'menu.index' dengan data yang diperlukan.
     */
    public function index(Request $request)
    {
        // Mengambil semua kategori menu dari database.
        // `with('subcategories')` adalah eager loading. Ini akan mengambil subkategori
        // yang terkait dengan setiap kategori menu dalam satu query tambahan,
        // mencegah masalah N+1 query dan meningkatkan performa.
        $categories = MenuCategory::with('subcategories')->get();

        // Mengambil nilai parameter 'category' dan 'subcategory' dari URL query string.
        // Contoh: ?category=makanan-utama&subcategory=nasi-goreng.
        $selectedCategorySlug = $request->query('category');
        $selectedSubcategorySlug = $request->query('subcategory');

        // Inisialisasi variabel untuk menyimpan objek kategori dan subkategori yang dipilih.
        $selectedCategory = null;
        $selectedSubcategory = null;

        // Memeriksa apakah ada slug kategori yang dipilih dari URL.
        if ($selectedCategorySlug) {
            // Jika ada, cari kategori menu berdasarkan slug tersebut.
            // `first()` akan mengembalikan objek kategori pertama yang cocok atau null jika tidak ditemukan.
            $selectedCategory = MenuCategory::where('slug', $selectedCategorySlug)->first();
        }

        // Memeriksa apakah ada slug subkategori yang dipilih dari URL.
        if ($selectedSubcategorySlug) {
            // Jika ada, cari subkategori menu berdasarkan slug tersebut.
            $selectedSubcategory = MenuSubcategory::where('slug', $selectedSubcategorySlug)->first();
        }

        // Mengambil subkategori yang relevan.
        // Jika ada kategori yang dipilih, ambil hanya subkategori di bawah kategori itu.
        // Jika tidak ada kategori yang dipilih, ambil semua subkategori.
        $subcategories = $selectedCategory
            ? MenuSubcategory::where('menu_category_id', $selectedCategory->id)->get()
            : MenuSubcategory::all();

        // Memulai query untuk mengambil item menu.
        // `with('variants')` adalah eager loading untuk mengambil varian menu (misal: ukuran, level pedas).
        $menus = Menu::with('variants');

        // Menerapkan filter menu berdasarkan subkategori atau kategori yang dipilih.
        if ($selectedSubcategory) {
            // Jika subkategori dipilih, filter menu hanya yang terkait dengan subkategori tersebut.
            $menus->where('menu_subcategory_id', $selectedSubcategory->id);
        } elseif ($selectedCategory) {
            // Jika hanya kategori yang dipilih (dan tidak ada subkategori),
            // ambil semua ID subkategori yang ada di bawah kategori tersebut.
            $subcatIds = MenuSubcategory::where('menu_category_id', $selectedCategory->id)->pluck('id');
            // Filter menu yang `menu_subcategory_id`-nya ada dalam daftar ID subkategori tersebut.
            $menus->whereIn('menu_subcategory_id', $subcatIds);
        }

        // Mengeksekusi query dan mendapatkan semua item menu yang sudah difilter.
        $menus = $menus->get();

        // Inisialisasi variabel untuk addon dan path PDF yang terkait dengan subkategori.
        $addon = null;
        $pdfPath = null;

        // Jika ada subkategori yang dipilih, ambil addon dan path PDF-nya.
        if ($selectedSubcategory) {
            // Ambil detail subkategori lagi, kali ini dengan eager load 'addon' jika ada relasi.
            $subcategory = MenuSubcategory::with('addon')->find($selectedSubcategory->id);
            // Ambil objek addon jika ada. `?->` adalah nullsafe operator.
            $addon = $subcategory?->addon;
            // Ambil path PDF jika ada.
            $pdfPath = $subcategory?->pdf_path;
        }

        // Mengembalikan view 'menu.index' (yang terletak di resources/views/menu/index.blade.php).
        // Meneruskan semua variabel yang telah disiapkan ke view.
        return view('menu.index', [
            'categories' => $categories,             // Semua kategori menu (dengan subkategori).
            'subcategories' => $subcategories,       // Subkategori yang relevan (semua atau berdasarkan kategori).
            'menus' => $menus,                       // Item menu yang sudah difilter.
            'addon' => $addon,                       // Addon terkait subkategori yang dipilih (jika ada).
            'selectedCategory' => $selectedCategory, // Objek kategori yang sedang aktif/dipilih.
            'selectedSubcategory' => $selectedSubcategory, // Objek subkategori yang sedang aktif/dipilih.
            'pdfPath' => $pdfPath,                   // Path PDF terkait subkategori yang dipilih (jika ada).
        ]);
    }
}
