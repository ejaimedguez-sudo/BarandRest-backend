<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItemCostHistory extends Model
{
    protected $fillable = [
        'menu_item_id',
        'action',
        'actor_role',
        'previous_cost',
        'new_cost',
        'difference',
        'context',
    ];

    protected $casts = [
        'context' => 'array',
    ];

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class, 'menu_item_id');
    }
}
