<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuAddon extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['title', 'description'];

    public function subcategories()
    {
        return $this->hasMany(MenuSubcategory::class);
    }
}
