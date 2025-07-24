<?php

use App\Http\Controllers\GalleryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MenuController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery');

Route::get('/menu', [MenuController::class, 'index'])->name('menu');

Route::get('/test-error/{code}', function ($code) {
    abort((int) $code);
});
