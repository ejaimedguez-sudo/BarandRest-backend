<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // Products
        DB::table('products')->insert([
            ['name' => 'Limon', 'stock' => 100, 'reorder_level' => 10, 'unit' => 'pcs', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Azucar', 'stock' => 50, 'reorder_level' => 5, 'unit' => 'kg', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ron', 'stock' => 80, 'reorder_level' => 10, 'unit' => 'l', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Menu items
        DB::table('menu_items')->insert([
            ['name' => 'Mojito', 'description' => 'Refrescante', 'price' => 5.50, 'cost' => 2.50, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cuba Libre', 'description' => 'Clasico', 'price' => 4.50, 'cost' => 2.00, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Orders and items
        $orderId = DB::table('orders')->insertGetId([
            'table_id' => 1,
            'user_id' => null,
            'total' => 10.00,
            'cost' => 5.00,
            'status' => 'closed',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $menuItem = DB::table('menu_items')->first();
        DB::table('order_items')->insert([
            'order_id' => $orderId,
            'menu_item_id' => $menuItem->id,
            'quantity' => 2,
            'unit_price' => $menuItem->price,
            'cost' => $menuItem->cost,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
