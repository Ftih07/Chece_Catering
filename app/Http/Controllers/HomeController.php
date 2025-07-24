<?php

namespace App\Http\Controllers;

use App\Models\GalleryCategory;
use App\Models\MenuCategory;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    //
    public function index()
    {
        $menuCategories = MenuCategory::with('thumbnail')->get();

        $galleryCategories = GalleryCategory::with(['galleries' => function ($query) {
            $query->limit(1);
        }])->get();

        return view('home', compact('menuCategories', 'galleryCategories'));
    }
}
