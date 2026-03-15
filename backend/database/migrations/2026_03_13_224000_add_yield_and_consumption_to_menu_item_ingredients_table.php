<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('menu_item_ingredients')) {
            return;
        }

        Schema::table('menu_item_ingredients', function (Blueprint $table) {
            if (!Schema::hasColumn('menu_item_ingredients', 'cocktail_yield')) {
                $table->decimal('cocktail_yield', 10, 3)->nullable()->after('quantity');
            }

            if (!Schema::hasColumn('menu_item_ingredients', 'consumption_ml')) {
                $table->decimal('consumption_ml', 10, 3)->nullable()->after('cocktail_yield');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('menu_item_ingredients')) {
            return;
        }

        Schema::table('menu_item_ingredients', function (Blueprint $table) {
            if (Schema::hasColumn('menu_item_ingredients', 'consumption_ml')) {
                $table->dropColumn('consumption_ml');
            }

            if (Schema::hasColumn('menu_item_ingredients', 'cocktail_yield')) {
                $table->dropColumn('cocktail_yield');
            }
        });
    }
};
