<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'sku',
        'product_type_id',
        'name',
        'presentation',
        'image_url',
        'unit',
        'cost',
        'stock',
        'daily_consumption',
        'coverage_days',
        'initial_stock',
        'stock_min',
        'stock_max',
        'reorder_level',
        'reorder_point',
    ];

    public function productType()
    {
        return $this->belongsTo(ProductType::class, 'product_type_id');
    }

    public function menuItemIngredients()
    {
        return $this->hasMany(MenuItemIngredient::class, 'product_id');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'product_id');
    }
}
