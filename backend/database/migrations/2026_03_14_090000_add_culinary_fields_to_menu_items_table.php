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
            if (!Schema::hasColumn('menu_items', 'prep_time_minutes')) {
                $table->unsignedSmallInteger('prep_time_minutes')->nullable()->after('description');
            }
            if (!Schema::hasColumn('menu_items', 'dish')) {
                $table->string('dish', 120)->nullable()->after('prep_time_minutes');
            }
            if (!Schema::hasColumn('menu_items', 'kitchen')) {
                $table->string('kitchen', 120)->nullable()->after('dish');
            }
            if (!Schema::hasColumn('menu_items', 'servings')) {
                $table->unsignedSmallInteger('servings')->nullable()->after('kitchen');
            }
            if (!Schema::hasColumn('menu_items', 'calories')) {
                $table->unsignedInteger('calories')->nullable()->after('servings');
            }
            if (!Schema::hasColumn('menu_items', 'equipment')) {
                $table->string('equipment', 255)->nullable()->after('calories');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('menu_items')) {
            return;
        }

        Schema::table('menu_items', function (Blueprint $table) {
            if (Schema::hasColumn('menu_items', 'equipment')) {
                $table->dropColumn('equipment');
            }
            if (Schema::hasColumn('menu_items', 'calories')) {
                $table->dropColumn('calories');
            }
            if (Schema::hasColumn('menu_items', 'servings')) {
                $table->dropColumn('servings');
            }
            if (Schema::hasColumn('menu_items', 'kitchen')) {
                $table->dropColumn('kitchen');
            }
            if (Schema::hasColumn('menu_items', 'dish')) {
                $table->dropColumn('dish');
            }
            if (Schema::hasColumn('menu_items', 'prep_time_minutes')) {
                $table->dropColumn('prep_time_minutes');
            }
        });
    }
};
