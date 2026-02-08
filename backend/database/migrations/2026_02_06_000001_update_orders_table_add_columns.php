<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (!Schema::hasColumn('orders', 'table_id')) {
                    $table->unsignedBigInteger('table_id')->nullable()->after('id');
                }
                if (!Schema::hasColumn('orders', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->nullable()->after('table_id');
                }
                if (!Schema::hasColumn('orders', 'total')) {
                    $table->decimal('total', 10, 2)->default(0)->after('user_id');
                }
                if (!Schema::hasColumn('orders', 'cost')) {
                    $table->decimal('cost', 10, 2)->default(0)->after('total');
                }
                if (!Schema::hasColumn('orders', 'status')) {
                    $table->enum('status', ['open', 'closed', 'cancelled'])->default('open')->after('cost');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (Schema::hasColumn('orders', 'status')) {
                    $table->dropColumn('status');
                }
                if (Schema::hasColumn('orders', 'cost')) {
                    $table->dropColumn('cost');
                }
                if (Schema::hasColumn('orders', 'total')) {
                    $table->dropColumn('total');
                }
                if (Schema::hasColumn('orders', 'user_id')) {
                    $table->dropColumn('user_id');
                }
                if (Schema::hasColumn('orders', 'table_id')) {
                    $table->dropColumn('table_id');
                }
            });
        }
    }
};
