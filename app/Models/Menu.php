<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['menu_subcategory_id', 'name', 'description', 'price', 'image'];

    public function subcategory()
    {
        return $this->belongsTo(MenuSubcategory::class, 'menu_subcategory_id');
    }

    public function variants()
    {
        return $this->hasMany(MenuVariant::class);
    }
}
