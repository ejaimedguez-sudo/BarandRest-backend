<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('measures')) {
            Schema::create('measures', function (Blueprint $table) {
                $table->id();
                $table->string('name', 120)->unique();
                $table->string('abbreviation', 20)->nullable()->unique();
                $table->string('description', 500)->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('measures');
    }
};
