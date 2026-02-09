<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('table_restaurants')) {
            Schema::create('table_restaurants', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->integer('capacity')->default(1);
                $table->string('location')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_restaurants');
    }
};
