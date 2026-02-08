<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('orders') && !Schema::hasColumn('orders', 'updated_at')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->timestamp('updated_at')->nullable()->after('created_at');
            });
        }

        if (Schema::hasTable('order_items') && !Schema::hasColumn('order_items', 'updated_at')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->timestamp('updated_at')->nullable()->after('created_at');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'updated_at')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('updated_at');
            });
        }

        if (Schema::hasTable('order_items') && Schema::hasColumn('order_items', 'updated_at')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->dropColumn('updated_at');
            });
        }
    }
};
