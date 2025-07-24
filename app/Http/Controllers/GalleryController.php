<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use App\Models\GalleryCategory;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function index(Request $request)
    {
        $categories = GalleryCategory::all();
        $selectedSlug = $request->get('category');

        $selectedCategory = null;
        if ($selectedSlug) {
            $selectedCategory = GalleryCategory::where('slug', $selectedSlug)->first();
        }

        $galleries = Gallery::when($selectedCategory, function ($query) use ($selectedCategory) {
            $query->where('gallery_category_id', $selectedCategory->id);
        })->get();

        return view('gallery.index', compact('categories', 'galleries', 'selectedCategory', 'selectedSlug'));
    }
}
