<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('products')) {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'initial_stock')) {
                $table->decimal('initial_stock', 10, 3)->nullable()->after('stock');
            }
            if (! Schema::hasColumn('products', 'stock_min')) {
                $table->decimal('stock_min', 10, 3)->nullable()->after('initial_stock');
            }
            if (! Schema::hasColumn('products', 'stock_max')) {
                $table->decimal('stock_max', 10, 3)->nullable()->after('stock_min');
            }
            if (! Schema::hasColumn('products', 'reorder_point')) {
                $table->decimal('reorder_point', 10, 3)->nullable()->after('stock_max');
            }
        });

        if (Schema::hasColumn('products', 'reorder_level') && Schema::hasColumn('products', 'reorder_point')) {
            DB::table('products')
                ->whereNull('reorder_point')
                ->whereNotNull('reorder_level')
                ->update(['reorder_point' => DB::raw('reorder_level')]);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('products')) {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'reorder_point')) {
                $table->dropColumn('reorder_point');
            }
            if (Schema::hasColumn('products', 'stock_max')) {
                $table->dropColumn('stock_max');
            }
            if (Schema::hasColumn('products', 'stock_min')) {
                $table->dropColumn('stock_min');
            }
            if (Schema::hasColumn('products', 'initial_stock')) {
                $table->dropColumn('initial_stock');
            }
        });
    }
};
