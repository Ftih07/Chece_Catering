<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuCategory extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['name'];

    public function galleries()
    {
        return $this->hasMany(GalleryMenuCategory::class);
    }

    public function subcategories()
    {
        return $this->hasMany(MenuSubcategory::class);
    }

    public function thumbnail()
    {
        // Ambil satu gambar saja (pertama)
        return $this->hasOne(GalleryMenuCategory::class)->latest();
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if (!$model->slug) {
                $model->slug = Str::slug($model->name . '-' . uniqid());
            }
        });
    }
}
