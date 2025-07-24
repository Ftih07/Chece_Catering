<?php

namespace App\Http\Controllers;

use App\Models\MenuCategory;
use App\Models\Menu;
use App\Models\MenuAddon;
use App\Models\MenuSubcategory;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    //
    public function index(Request $request)
    {
        $categories = MenuCategory::with('subcategories')->get();

        $selectedCategorySlug = $request->query('category');
        $selectedSubcategorySlug = $request->query('subcategory');

        $selectedCategory = null;
        $selectedSubcategory = null;

        if ($selectedCategorySlug) {
            $selectedCategory = MenuCategory::where('slug', $selectedCategorySlug)->first();
        }

        if ($selectedSubcategorySlug) {
            $selectedSubcategory = MenuSubcategory::where('slug', $selectedSubcategorySlug)->first();
        }

        $subcategories = $selectedCategory
            ? MenuSubcategory::where('menu_category_id', $selectedCategory->id)->get()
            : MenuSubcategory::all();

        $menus = Menu::with('variants');

        if ($selectedSubcategory) {
            $menus->where('menu_subcategory_id', $selectedSubcategory->id);
        } elseif ($selectedCategory) {
            $subcatIds = MenuSubcategory::where('menu_category_id', $selectedCategory->id)->pluck('id');
            $menus->whereIn('menu_subcategory_id', $subcatIds);
        }

        $menus = $menus->get();

        $addon = null;
        $pdfPath = null;

        if ($selectedSubcategory) {
            $subcategory = MenuSubcategory::with('addon')->find($selectedSubcategory->id);
            $addon = $subcategory?->addon;
            $pdfPath = $subcategory?->pdf_path;
        }

        return view('menu.index', [
            'categories' => $categories,
            'subcategories' => $subcategories,
            'menus' => $menus,
            'addon' => $addon,
            'selectedCategory' => $selectedCategory,
            'selectedSubcategory' => $selectedSubcategory,
            'pdfPath' => $pdfPath,
        ]);
    }
}
