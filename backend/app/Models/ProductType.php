<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductType extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'product_type_id');
    }

    public function menuItems()
    {
        return $this->hasMany(MenuItem::class, 'product_type_id');
    }
}
