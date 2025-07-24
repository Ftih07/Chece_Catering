<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Gallery extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['gallery_category_id', 'name', 'description', 'image'];

    public function category()
    {
        return $this->belongsTo(GalleryCategory::class, 'gallery_category_id');
    }
}
