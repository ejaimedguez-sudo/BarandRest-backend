<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                if (!Schema::hasColumn('products', 'name')) {
                    $table->string('name')->after('id')->nullable();
                }
                if (!Schema::hasColumn('products', 'stock')) {
                    // Use decimal for stock to allow fractional quantities (e.g., liters)
                    $table->decimal('stock', 10, 3)->default(0)->after('name');
                }
                if (!Schema::hasColumn('products', 'reorder_level')) {
                    $table->integer('reorder_level')->default(0)->after('stock');
                }
                if (!Schema::hasColumn('products', 'unit')) {
                    $table->string('unit')->nullable()->after('reorder_level');
                }
                if (!Schema::hasColumn('products', 'cost')) {
                    $table->decimal('cost', 10, 2)->default(0)->after('unit');
                }
                if (!Schema::hasColumn('products', 'sku')) {
                    $table->string('sku')->nullable()->after('cost');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                if (Schema::hasColumn('products', 'reorder_level')) {
                    $table->dropColumn('reorder_level');
                }
                if (Schema::hasColumn('products', 'stock')) {
                    $table->dropColumn('stock');
                }
                if (Schema::hasColumn('products', 'name')) {
                    $table->dropColumn('name');
                }
            });
        }
    }
};
