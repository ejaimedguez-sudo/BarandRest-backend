<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('product_types')) {
            Schema::create('product_types', function (Blueprint $table) {
                $table->id();
                $table->string('code', 80)->nullable()->unique();
                $table->string('name', 120)->unique();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('products') && ! Schema::hasColumn('products', 'product_type_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->unsignedBigInteger('product_type_id')->nullable()->index()->after('name');
            });
        }

        if (Schema::hasTable('menu_items') && ! Schema::hasColumn('menu_items', 'product_type_id')) {
            Schema::table('menu_items', function (Blueprint $table) {
                $table->unsignedBigInteger('product_type_id')->nullable()->index()->after('name');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('products') && Schema::hasColumn('products', 'product_type_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('product_type_id');
            });
        }

        if (Schema::hasTable('menu_items') && Schema::hasColumn('menu_items', 'product_type_id')) {
            Schema::table('menu_items', function (Blueprint $table) {
                $table->dropColumn('product_type_id');
            });
        }

        Schema::dropIfExists('product_types');
    }
};
