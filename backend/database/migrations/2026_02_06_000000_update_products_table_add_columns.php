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
                    $table->integer('stock')->default(0)->after('name');
                }
                if (!Schema::hasColumn('products', 'reorder_level')) {
                    $table->integer('reorder_level')->default(0)->after('stock');
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
