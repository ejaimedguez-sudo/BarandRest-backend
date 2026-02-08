<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $fillable = ['code','name','description','price','cost','category','is_recipe'];

    public function ingredients()
    {
        return $this->hasMany(MenuItemIngredient::class, 'menu_item_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'menu_item_id');
    }

    /**
     * Calculate cost from ingredients (simple sum: ingredient.quantity * product.cost)
     */
    public function calculateCostFromIngredients(): float
    {
        $total = 0.0;
        $this->loadMissing('ingredients.product');
        foreach ($this->ingredients as $ing) {
            $product = $ing->product;
            $qty = is_numeric($ing->quantity) ? (float)$ing->quantity : 0.0;
            $cost = $product->cost ?? 0.0;
            $total += $qty * $cost;
        }
        return round($total, 4);
    }

    /**
     * Suggest price based on desired margin percent (e.g., 30 means 30%)
     */
    public function suggestPrice(float $marginPercent = 30.0): ?float
    {
        $cost = $this->calculateCostFromIngredients();
        if ($cost <= 0) {
            return null;
        }
        $margin = max(0.0, min(99.0, $marginPercent)) / 100.0;
        if ($margin >= 1.0) return null;
        $price = $cost / (1 - $margin);
        return round($price, 2);
    }
}
