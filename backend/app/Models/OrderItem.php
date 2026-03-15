<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = ['order_id', 'menu_item_id', 'quantity', 'unit_price', 'price', 'cost', 'notes'];

    protected $casts = [
        'quantity' => 'float',
        'unit_price' => 'float',
        'cost' => 'float',
    ];

    public function setPriceAttribute($value): void
    {
        $this->attributes['unit_price'] = $value;
    }

    public function getPriceAttribute()
    {
        return $this->attributes['unit_price'] ?? null;
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class, 'menu_item_id');
    }
}
