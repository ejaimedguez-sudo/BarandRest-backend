<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('products')) {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'presentation')) {
                $table->string('presentation', 120)->nullable()->after('name');
            }

            if (!Schema::hasColumn('products', 'daily_consumption')) {
                $table->decimal('daily_consumption', 10, 3)->nullable()->after('stock');
            }

            if (!Schema::hasColumn('products', 'coverage_days')) {
                $table->decimal('coverage_days', 10, 2)->nullable()->after('daily_consumption');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('products')) {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'coverage_days')) {
                $table->dropColumn('coverage_days');
            }

            if (Schema::hasColumn('products', 'daily_consumption')) {
                $table->dropColumn('daily_consumption');
            }

            if (Schema::hasColumn('products', 'presentation')) {
                $table->dropColumn('presentation');
            }
        });
    }
};
