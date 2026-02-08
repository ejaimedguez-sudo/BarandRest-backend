<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    public function run()
    {
        // Create a product (e.g., alcohol base)
        $productId = DB::table('products')->insertGetId([
            'sku' => 'TEST-001',
            'name' => 'Alcohol Base',
            'unit' => 'L',
            'cost' => 5.00,
            'stock' => 10.000,
            'reorder_point' => 1.000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create a menu item (cocktail)
        $menuItemId = DB::table('menu_items')->insertGetId([
            'code' => 'COCK-001',
            'name' => 'Test Cocktail',
            'description' => 'Cocktail de prueba',
            'price' => 12.00,
            'cost' => 0.00,
            'category' => 'Bebidas',
            'is_recipe' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Link ingredient (0.1 L per cocktail)
        DB::table('menu_item_ingredients')->insert([
            'menu_item_id' => $menuItemId,
            'product_id' => $productId,
            'quantity' => 0.100,
            'unit' => 'L',
        ]);

        // Create a waiter user
        $userId = DB::table('users')->insertGetId([
            'name' => 'Test Waiter',
            'email' => 'waiter@example.test',
            'password' => Hash::make('secret'),
            'role' => 'waiter',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create a table
        $tableId = DB::table('tables_restaurant')->insertGetId([
            'name' => 'Mesa 1',
            'capacity' => 4,
            'location' => 'Salón',
            'notes' => 'Mesa de prueba',
        ]);

        // Create an order inside Feb 2026
        $orderId = DB::table('orders')->insertGetId([
            'table_id' => $tableId,
            'customer_id' => null,
            'waiter_id' => $userId,
            'status' => 'closed',
            'total' => 12.00,
            'created_at' => '2026-02-10 12:00:00',
            'closed_at' => '2026-02-10 12:30:00',
        ]);

        // Create order item
        DB::table('order_items')->insert([
            'order_id' => $orderId,
            'menu_item_id' => $menuItemId,
            'quantity' => 1,
            'unit_price' => 12.00,
            'cost' => 0.50,
            'notes' => 'Test order item',
        ]);

        // Adjust product stock accordingly (subtract 0.1 L)
        DB::table('products')->where('id', $productId)->decrement('stock', 0.100);

        // Record a stock movement
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
