<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = ['order_id','menu_item_id','quantity','price','cost','notes'];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class, 'menu_item_id');
    }
}
