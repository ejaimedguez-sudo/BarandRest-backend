<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;

class ExampleDataSeeder extends Seeder
{
    public function run()
    {
        // Users
        if (Schema::hasTable('users')) {
            $insertUser = function ($email, $name, $password) {
                $exists = DB::table('users')->where('email', $email)->exists();
                if (! $exists) {
                    DB::table('users')->insert([
                        'name' => $name,
                        'email' => $email,
                        'password' => Hash::make($password),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            };

            $insertUser('admin@example.com', 'Admin Demo', 'password');
            $insertUser('waiter@example.com', 'Mesero Demo', 'password');
            $insertUser('kitchen@example.com', 'Cocina Demo', 'password');
        }

        // Bars / Locations
        if (Schema::hasTable('bars')) {
            DB::table('bars')->updateOrInsert(
                ['slug' => 'demo-bar'],
                ['name' => 'Bar Demo', 'address' => 'Calle Falsa 123', 'created_at' => now(), 'updated_at' => now()]
            );
        }

        // Tables (mesas)
        if (Schema::hasTable('tables')) {
            for ($i = 1; $i <= 8; $i++) {
                DB::table('tables')->updateOrInsert(
                    ['code' => 'MESA-'.$i],
                    ['name' => 'Mesa '.$i, 'capacity' => 4, 'created_at' => now(), 'updated_at' => now()]
                );
            }
        }

        // Products
        if (Schema::hasTable('products')) {
            $products = [
                ['name' => 'Cerveza', 'price' => 2.50],
                ['name' => 'Copa de vino', 'price' => 3.50],
                ['name' => 'Hamburguesa', 'price' => 6.50],
                ['name' => 'Ensalada', 'price' => 4.00],
            ];
            foreach ($products as $p) {
                DB::table('products')->updateOrInsert(
                    ['name' => $p['name']],
                    ['price' => $p['price'], 'created_at' => now(), 'updated_at' => now()]
                );
            }
        }

        // Inventory
        if (Schema::hasTable('inventories')) {
            $items = [
                ['item' => 'Lata de cerveza', 'quantity' => 120],
                ['item' => 'Botella de vino', 'quantity' => 40],
                ['item' => 'Pan de hamburguesa', 'quantity' => 80],
            ];
            foreach ($items as $it) {
                DB::table('inventories')->updateOrInsert(
                    ['item' => $it['item']],
                    ['quantity' => $it['quantity'], 'created_at' => now(), 'updated_at' => now()]
                );
            }
        }

        // Recipes
        if (Schema::hasTable('recipes')) {
            DB::table('recipes')->updateOrInsert(
                ['name' => 'Hamburguesa Clasica'],
                ['instructions' => 'Armar con pan, carne, lechuga y salsa', 'created_at' => now(), 'updated_at' => now()]
            );
        }

        // Sales / orders sample
        if (Schema::hasTable('sales')) {
            $exists = DB::table('sales')->where('reference', 'DEMO-0001')->exists();
            if (! $exists) {
                DB::table('sales')->insert([
                    'reference' => 'DEMO-0001',
                    'table_code' => 'MESA-1',
                    'total' => 15.00,
                    'created_at' => now()->subDay(),
                    'updated_at' => now()->subDay(),
                ]);
            }
        }

        // Commissions example
        if (Schema::hasTable('commissions')) {
            DB::table('commissions')->updateOrInsert(
                ['label' => 'Comision Meseros'],
                ['rate' => 0.05, 'created_at' => now(), 'updated_at' => now()]
            );
        }

        // Reports settings
        if (Schema::hasTable('report_settings')) {
            DB::table('report_settings')->updateOrInsert(
                ['key' => 'daily_report_time'],
                ['value' => '03:00', 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
