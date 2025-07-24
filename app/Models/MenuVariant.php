<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuVariant extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['menu_id', 'name'];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}
