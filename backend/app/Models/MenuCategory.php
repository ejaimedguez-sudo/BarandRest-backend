<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuCategory extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'sort_order',
    ];

    public function menuItems()
    {
        return $this->hasMany(MenuItem::class, 'menu_category_id');
    }
}
