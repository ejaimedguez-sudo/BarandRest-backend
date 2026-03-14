<?php

namespace Tests\Feature;

use App\Models\MenuItem;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CatalogImageCleanupCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_cleanup_command_removes_only_unlinked_managed_images(): void
    {
        Storage::fake('public');

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('public');

        $referencedByProduct = 'catalog-images/test/referenced-product.jpg';
        $referencedByMenuItem = 'catalog-images/test/referenced-item.jpg';
        $orphanPath = 'catalog-images/test/orphan.jpg';
        $outsideManagedPath = 'other-folder/keep-me.jpg';

        $disk->put($referencedByProduct, 'a');
        $disk->put($referencedByMenuItem, 'b');
        $disk->put($orphanPath, 'c');
        $disk->put($outsideManagedPath, 'd');

        Product::query()->create([
            'sku' => 'PRD-REF-1',
            'name' => 'Producto Referencia',
            'unit' => 'pieza',
            'image_url' => '/storage/' . $referencedByProduct,
        ]);

        MenuItem::query()->create([
            'code' => 'MI-REF-1',
            'name' => 'Item Referencia',
            'price' => 100,
            'cost' => 50,
            'profit_margin_percent' => 50,
            'is_recipe' => false,
            'image_url' => 'https://example.com/storage/' . $referencedByMenuItem,
        ]);

        $this->artisan('catalog:cleanup-orphan-images', ['--older-than-minutes' => 0])
            ->assertSuccessful();

        $disk->assertExists($referencedByProduct);
        $disk->assertExists($referencedByMenuItem);
        $disk->assertExists($outsideManagedPath);
        $disk->assertMissing($orphanPath);
    }

    public function test_cleanup_command_dry_run_does_not_delete_files(): void
    {
        Storage::fake('public');

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('public');

        $orphanPath = 'catalog-images/test/orphan-dry-run.jpg';
        $disk->put($orphanPath, 'orphan');

        $this->artisan('catalog:cleanup-orphan-images', [
            '--older-than-minutes' => 0,
            '--dry-run' => true,
        ])->assertSuccessful();

        $disk->assertExists($orphanPath);
    }
}
