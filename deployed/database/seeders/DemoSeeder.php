<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // Products (idempotent)
        $products = [
            ['name' => 'Limon', 'stock' => 100, 'reorder_level' => 10, 'unit' => 'pcs'],
            ['name' => 'Azucar', 'stock' => 50, 'reorder_level' => 5, 'unit' => 'kg'],
            ['name' => 'Ron', 'stock' => 80, 'reorder_level' => 10, 'unit' => 'l'],
        ];
        foreach ($products as $p) {
            DB::table('products')->updateOrInsert(
                ['name' => $p['name']],
                array_merge($p, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        // Menu items (idempotent)
        $menuItems = [
            ['name' => 'Mojito', 'description' => 'Refrescante', 'price' => 5.50, 'cost' => 2.50],
            ['name' => 'Cuba Libre', 'description' => 'Clasico', 'price' => 4.50, 'cost' => 2.00],
        ];
        foreach ($menuItems as $m) {
            DB::table('menu_items')->updateOrInsert(
                ['name' => $m['name']],
                array_merge($m, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        // Orders and items
        // Orders and items (create if not exists)
        $existingOrder = DB::table('orders')->where('table_id', 1)->where('total', 10.00)->first();
        if ($existingOrder) {
            $orderId = $existingOrder->id;
            $createdNewOrder = false;
        } else {
            $orderId = DB::table('orders')->insertGetId([
                'table_id' => 1,
                'user_id' => null,
                'total' => 10.00,
                'cost' => 5.00,
                'status' => 'closed',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $createdNewOrder = true;
        }

        $menuItem = DB::table('menu_items')->where('name', 'Mojito')->first();
        if ($menuItem) {
            $existsItem = DB::table('order_items')->where('order_id', $orderId)->where('menu_item_id', $menuItem->id)->first();
            if (! $existsItem) {
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
    }
}
