<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuSubcategory extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'menu_category_id',
        'menu_addon_id',
        'name',
        'pdf_path'
    ];

    public function category()
    {
        return $this->belongsTo(MenuCategory::class, 'menu_category_id');
    }

    public function addon()
    {
        return $this->belongsTo(MenuAddon::class, 'menu_addon_id');
    }

    public function menus()
    {
        return $this->hasMany(Menu::class);
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
