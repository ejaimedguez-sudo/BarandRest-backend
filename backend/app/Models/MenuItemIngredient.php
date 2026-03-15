<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItemIngredient extends Model
{
    protected $fillable = ['menu_item_id', 'product_id', 'quantity', 'cocktail_yield', 'consumption_ml', 'unit'];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class, 'menu_item_id');
    }
}
