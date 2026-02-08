<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['sku','name','unit','cost','stock','reorder_point'];

    public function menuItemIngredients()
    {
        return $this->hasMany(MenuItemIngredient::class, 'product_id');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'product_id');
    }
}
