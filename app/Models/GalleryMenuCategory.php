<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GalleryMenuCategory extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['menu_category_id', 'name', 'image'];

    public function category()
    {
        return $this->belongsTo(MenuCategory::class);
    }
}
