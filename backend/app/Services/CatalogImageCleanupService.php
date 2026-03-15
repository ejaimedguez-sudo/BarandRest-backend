<?php

namespace App\Services;

use App\Models\MenuItem;
use App\Models\Product;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;

class CatalogImageCleanupService
{
    public function deleteIfOrphaned(?string $imageUrl, ?int $excludeProductId = null, ?int $excludeMenuItemId = null): void
    {
        $normalizedUrl = trim((string) $imageUrl);
        if ($normalizedUrl === '') {
            return;
        }

        $path = $this->managedPathFromUrl($normalizedUrl);
        if ($path === null) {
            return;
        }

        if ($this->hasReferencesByPath($path, $excludeProductId, $excludeMenuItemId)) {
            return;
        }

        Storage::disk('public')->delete($path);
    }

    public function cleanupUnlinkedManagedFiles(int $olderThanMinutes = 180, bool $dryRun = false): array
    {
        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('public');
        $cutoffTimestamp = now()->subMinutes(max(0, $olderThanMinutes))->getTimestamp();
        $referencedPaths = $this->referencedManagedPaths();
        $allManagedFiles = $disk->allFiles('catalog-images');

        $deleted = [];
        $keptRecent = [];
        $keptReferenced = [];

        foreach ($allManagedFiles as $path) {
            $normalizedPath = trim((string) $path);
            if ($normalizedPath === '') {
                continue;
            }

            if (isset($referencedPaths[$normalizedPath])) {
                $keptReferenced[] = $normalizedPath;

                continue;
            }

            $lastModified = $this->safeLastModified($disk, $normalizedPath);
            if ($lastModified !== null && $lastModified > $cutoffTimestamp) {
                $keptRecent[] = $normalizedPath;

                continue;
            }

            if (! $dryRun) {
                $disk->delete($normalizedPath);
            }

            $deleted[] = $normalizedPath;
        }

        return [
            'scanned' => count($allManagedFiles),
            'deleted' => $deleted,
            'kept_recent' => $keptRecent,
            'kept_referenced' => $keptReferenced,
            'dry_run' => $dryRun,
        ];
    }

    public function managedPathFromUrl(?string $imageUrl): ?string
    {
        return $this->extractManagedPathFromUrl($imageUrl);
    }

    private function hasReferencesByPath(string $managedPath, ?int $excludeProductId, ?int $excludeMenuItemId): bool
    {
        $productQuery = Product::query()
            ->select(['id', 'image_url'])
            ->whereNotNull('image_url')
            ->where('image_url', 'like', '%catalog-images/%');

        if ($excludeProductId !== null) {
            $productQuery->where('id', '!=', $excludeProductId);
        }

        foreach ($productQuery->cursor() as $product) {
            if ($this->extractManagedPathFromUrl($product->image_url) === $managedPath) {
                return true;
            }
        }

        $menuItemQuery = MenuItem::query()
            ->select(['id', 'image_url'])
            ->whereNotNull('image_url')
            ->where('image_url', 'like', '%catalog-images/%');

        if ($excludeMenuItemId !== null) {
            $menuItemQuery->where('id', '!=', $excludeMenuItemId);
        }

        foreach ($menuItemQuery->cursor() as $item) {
            if ($this->extractManagedPathFromUrl($item->image_url) === $managedPath) {
                return true;
            }
        }

        return false;
    }

    private function extractManagedPathFromUrl(?string $imageUrl): ?string
    {
        $normalizedUrl = trim((string) $imageUrl);
        if ($normalizedUrl === '') {
            return null;
        }

        $path = parse_url($normalizedUrl, PHP_URL_PATH);
        if (! is_string($path) || $path === '') {
            return null;
        }

        $normalizedPath = trim(rawurldecode($path));
        if (str_starts_with($normalizedPath, '/storage/')) {
            $normalizedPath = substr($normalizedPath, 9);
        } elseif (str_starts_with($normalizedPath, 'storage/')) {
            $normalizedPath = substr($normalizedPath, 8);
        } elseif (str_starts_with($normalizedPath, '/')) {
            $normalizedPath = ltrim($normalizedPath, '/');
        }

        if (! str_starts_with($normalizedPath, 'catalog-images/')) {
            $index = strpos($normalizedPath, 'catalog-images/');
            if ($index === false) {
                return null;
            }

            $normalizedPath = substr($normalizedPath, $index);
        }

        return $normalizedPath;
    }

    private function referencedManagedPaths(): array
    {
        $paths = [];

        Product::query()
            ->select(['id', 'image_url'])
            ->whereNotNull('image_url')
            ->where('image_url', '!=', '')
            ->chunkById(200, function ($products) use (&$paths): void {
                foreach ($products as $product) {
                    $path = $this->extractManagedPathFromUrl($product->image_url);
                    if ($path !== null) {
                        $paths[$path] = true;
                    }
                }
            });

        MenuItem::query()
            ->select(['id', 'image_url'])
            ->whereNotNull('image_url')
            ->where('image_url', '!=', '')
            ->chunkById(200, function ($items) use (&$paths): void {
                foreach ($items as $item) {
                    $path = $this->extractManagedPathFromUrl($item->image_url);
                    if ($path !== null) {
                        $paths[$path] = true;
                    }
                }
            });

        return $paths;
    }

    private function safeLastModified(FilesystemAdapter $disk, string $path): ?int
    {
        try {
            return $disk->lastModified($path);
        } catch (\Throwable $exception) {
            return null;
        }
    }
}
