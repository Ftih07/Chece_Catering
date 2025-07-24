<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Support\Str;

class GalleryCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'description', 'slug'];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            $category->slug = Str::slug($category->name);
        });

        static::updating(function ($category) {
            $category->slug = Str::slug($category->name);
        });
    }

    public function galleries()
    {
        return $this->hasMany(Gallery::class);
    }
}
