<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    public function run()
    {
        // Create a product (idempotent)
        DB::table('products')->updateOrInsert(
            ['sku' => 'TEST-001'],
            [
                'name' => 'Alcohol Base',
                'unit' => 'L',
                'cost' => 5.00,
                'stock' => 10.000,
                'reorder_point' => 1.000,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        $product = DB::table('products')->where('sku', 'TEST-001')->first();
        $productId = $product->id;

        // Create a menu item (idempotent)
        DB::table('menu_items')->updateOrInsert(
            ['code' => 'COCK-001'],
            [
                'name' => 'Test Cocktail',
                'description' => 'Cocktail de prueba',
                'price' => 12.00,
                'cost' => 0.00,
                'category' => 'Bebidas',
                'is_recipe' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        $menuItem = DB::table('menu_items')->where('code', 'COCK-001')->first();
        $menuItemId = $menuItem->id;

        // Link ingredient (idempotent)
        DB::table('menu_item_ingredients')->updateOrInsert(
            ['menu_item_id' => $menuItemId, 'product_id' => $productId],
            ['quantity' => 0.100, 'unit' => 'L']
        );

        // Create a waiter user (idempotent)
        DB::table('users')->updateOrInsert(
            ['email' => 'waiter@example.test'],
            ['name' => 'Test Waiter', 'password' => Hash::make('secret'), 'role' => 'waiter', 'created_at' => now(), 'updated_at' => now()]
        );
        $user = DB::table('users')->where('email', 'waiter@example.test')->first();
        $userId = $user->id;

        // Create a table (idempotent)
        DB::table('tables_restaurant')->updateOrInsert(
            ['name' => 'Mesa 1'],
            ['capacity' => 4, 'location' => 'Salón', 'notes' => 'Mesa de prueba']
        );
        $table = DB::table('tables_restaurant')->where('name', 'Mesa 1')->first();
        $tableId = $table->id;

        // Create or find an order for that date (idempotent)
        $order = DB::table('orders')->where('table_id', $tableId)->where('created_at', '2026-02-10 12:00:00')->first();
        $createdNewOrder = false;
        if (! $order) {
            $orderId = DB::table('orders')->insertGetId([
                'table_id' => $tableId,
                'customer_id' => null,
                'waiter_id' => $userId,
                'status' => 'closed',
                'total' => 12.00,
                'created_at' => '2026-02-10 12:00:00',
                'closed_at' => '2026-02-10 12:30:00',
            ]);
            $createdNewOrder = true;
        } else {
            $orderId = $order->id;
        }

        // Create order item if missing
        $existsOrderItem = DB::table('order_items')->where('order_id', $orderId)->where('menu_item_id', $menuItemId)->first();
        if (! $existsOrderItem) {
            DB::table('order_items')->insert([
                'order_id' => $orderId,
                'menu_item_id' => $menuItemId,
                'quantity' => 1,
                'unit_price' => 12.00,
                'cost' => 0.50,
                'notes' => 'Test order item',
            ]);
        }

        // Adjust product stock and record movement only when order was just created
        if ($createdNewOrder) {
            DB::table('products')->where('id', $productId)->decrement('stock', 0.100);
            DB::table('stock_movements')->insert([
                'product_id' => $productId,
                'quantity' => -0.100,
                'type' => 'out',
                'notes' => 'Test order consumption',
                'created_by' => $userId,
                'created_at' => '2026-02-10 12:00:00',
            ]);
        }
    }
}
