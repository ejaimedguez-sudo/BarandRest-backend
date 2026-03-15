<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('menu_items')) {
            return;
        }

        Schema::table('menu_items', function (Blueprint $table) {
            if (!Schema::hasColumn('menu_items', 'code')) {
                $table->string('code', 100)->nullable();
            }

            if (!Schema::hasColumn('menu_items', 'category')) {
                $table->string('category', 120)->nullable();
            }

            if (!Schema::hasColumn('menu_items', 'is_recipe')) {
                $table->boolean('is_recipe')->default(false);
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('menu_items')) {
            return;
        }

        Schema::table('menu_items', function (Blueprint $table) {
            if (Schema::hasColumn('menu_items', 'is_recipe')) {
                $table->dropColumn('is_recipe');
            }

            if (Schema::hasColumn('menu_items', 'category')) {
                $table->dropColumn('category');
            }

            if (Schema::hasColumn('menu_items', 'code')) {
                $table->dropColumn('code');
            }
        });
    }
};
