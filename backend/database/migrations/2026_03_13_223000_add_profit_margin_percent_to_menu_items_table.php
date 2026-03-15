<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('menu_items')) {
            return;
        }

        Schema::table('menu_items', function (Blueprint $table) {
            if (! Schema::hasColumn('menu_items', 'profit_margin_percent')) {
                $table->decimal('profit_margin_percent', 6, 2)->nullable()->after('cost');
            }
        });

        if (Schema::hasColumn('menu_items', 'price') && Schema::hasColumn('menu_items', 'cost')) {
            DB::table('menu_items')
                ->whereNull('profit_margin_percent')
                ->whereNotNull('cost')
                ->where('price', '>', 0)
                ->update([
                    'profit_margin_percent' => DB::raw('ROUND(((price - cost) / price) * 100, 2)'),
                ]);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('menu_items')) {
            return;
        }

        Schema::table('menu_items', function (Blueprint $table) {
            if (Schema::hasColumn('menu_items', 'profit_margin_percent')) {
                $table->dropColumn('profit_margin_percent');
            }
        });
    }
};
