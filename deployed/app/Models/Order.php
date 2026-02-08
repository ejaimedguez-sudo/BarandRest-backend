<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['table_id','customer_id','waiter_id','status','total'];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function table()
    {
        return $this->belongsTo(TableRestaurant::class, 'table_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
