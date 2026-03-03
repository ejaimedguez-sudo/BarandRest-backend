<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('orders')) {
            return;
        }

        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'customer_id')) {
                $table->unsignedBigInteger('customer_id')->nullable()->after('table_id');
                $table->index('customer_id');
            }

            if (!Schema::hasColumn('orders', 'waiter_id')) {
                $table->unsignedBigInteger('waiter_id')->nullable()->after('customer_id');
                $table->index('waiter_id');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('orders')) {
            return;
        }

        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'waiter_id')) {
                $table->dropIndex(['waiter_id']);
                $table->dropColumn('waiter_id');
            }

            if (Schema::hasColumn('orders', 'customer_id')) {
                $table->dropIndex(['customer_id']);
                $table->dropColumn('customer_id');
            }
        });
    }
};
