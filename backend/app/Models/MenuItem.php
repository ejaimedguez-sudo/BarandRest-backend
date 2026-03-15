<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $fillable = [
        'code',
        'name',
        'product_type_id',
        'menu_category_id',
        'description',
        'image_url',
        'price',
        'cost',
        'manual_cost',
        'profit_margin_percent',
        'category',
        'is_recipe',
        'prep_time_minutes',
        'dish',
        'kitchen',
        'servings',
        'calories',
        'equipment',
    ];

    public function productType()
    {
        return $this->belongsTo(ProductType::class, 'product_type_id');
    }

    public function menuCategory()
    {
        return $this->belongsTo(MenuCategory::class, 'menu_category_id');
    }

    public function ingredients()
    {
        return $this->hasMany(MenuItemIngredient::class, 'menu_item_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'menu_item_id');
    }

    /**
     * Calculate cost from ingredients.
     * - If ingredient has consumption_ml, cost is proportional to 1L bottle cost.
     * - Otherwise, fallback to quantity * product.cost.
     */
    public function calculateCostFromIngredients(): float
    {
        $total = 0.0;
        $this->loadMissing('ingredients.product');
        foreach ($this->ingredients as $ing) {
            $product = $ing->product;
            $cost = is_numeric($product->cost ?? null) ? (float) $product->cost : 0.0;

            if (is_numeric($ing->consumption_ml ?? null) && (float) $ing->consumption_ml > 0) {
                $consumptionMl = (float) $ing->consumption_ml;
                $total += ($cost / 1000.0) * $consumptionMl;

                continue;
            }

            $qty = is_numeric($ing->quantity ?? null) ? (float) $ing->quantity : 0.0;
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
        if ($margin >= 1.0) {
            return null;
        }
        $price = $cost / (1 - $margin);

        return round($price, 2);
    }
}
