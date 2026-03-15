<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('menu_categories')) {
            Schema::create('menu_categories', function (Blueprint $table) {
                $table->id();
                $table->string('code', 80)->nullable()->unique();
                $table->string('name', 120)->unique();
                $table->text('description')->nullable();
                $table->unsignedInteger('sort_order')->nullable();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('menu_items') && ! Schema::hasColumn('menu_items', 'menu_category_id')) {
            Schema::table('menu_items', function (Blueprint $table) {
                $table->unsignedBigInteger('menu_category_id')->nullable()->index()->after('product_type_id');
            });
        }

        if (! Schema::hasTable('menu_items') || ! Schema::hasColumn('menu_items', 'category') || ! Schema::hasColumn('menu_items', 'menu_category_id')) {
            return;
        }

        $rows = DB::table('menu_items')
            ->select('id', 'category')
            ->whereNotNull('category')
            ->where('category', '<>', '')
            ->orderBy('id')
            ->get();

        $cache = [];

        foreach ($rows as $row) {
            $name = trim((string) $row->category);
            if ($name === '') {
                continue;
            }

            if (! isset($cache[$name])) {
                $existing = DB::table('menu_categories')->where('name', $name)->first();

                if ($existing) {
                    $cache[$name] = (int) $existing->id;
                } else {
                    $baseCode = strtoupper(str_replace('_', '-', Str::slug($name, '_')));
                    $baseCode = $baseCode !== '' ? $baseCode : 'CAT';
                    $candidate = $baseCode;
                    $seq = 1;

                    while (DB::table('menu_categories')->where('code', $candidate)->exists()) {
                        $seq += 1;
                        $candidate = $baseCode.'-'.$seq;
                    }

                    $id = DB::table('menu_categories')->insertGetId([
                        'code' => $candidate,
                        'name' => $name,
                        'description' => null,
                        'sort_order' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $cache[$name] = (int) $id;
                }
            }

            DB::table('menu_items')
                ->where('id', $row->id)
                ->update(['menu_category_id' => $cache[$name]]);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('menu_items') && Schema::hasColumn('menu_items', 'menu_category_id')) {
            Schema::table('menu_items', function (Blueprint $table) {
                $table->dropColumn('menu_category_id');
            });
        }

        Schema::dropIfExists('menu_categories');
    }
};
