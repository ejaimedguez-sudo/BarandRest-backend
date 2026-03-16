<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CatalogImageCleanupService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    private function normalizeImageReferences(array $data): array
    {
        $imageUrl = trim((string) ($data['image_url'] ?? ''));
        $imagePath = trim((string) ($data['image_path'] ?? ''));

        if ($imageUrl === '' && $imagePath !== '') {
            $normalizedPath = ltrim($imagePath, '/');

            if (str_starts_with($normalizedPath, 'storage/')) {
                $normalizedPath = substr($normalizedPath, 8);
            }

            if (str_starts_with($normalizedPath, 'catalog-images/')) {
                $data['image_url'] = Storage::disk('public')->url($normalizedPath);
            }
        }

        unset($data['image_path']);

        return $data;
    }

    private function rowsFingerprintSignature(iterable $rows): string
    {
        $parts = [];

        foreach ($rows as $row) {
            $updatedAt = $row->updated_at;
            $updatedToken = $updatedAt ? $updatedAt->format('Y-m-d H:i:s.u') : '';
            $parts[] = implode('|', [
                (string) $row->id,
                $updatedToken,
                (string) ($row->image_url ?? ''),
            ]);
        }

        return sha1(implode(';', $parts));
    }

    private function applyCoverageFromConsumption(array $data, ?Product $existing = null): array
    {
        $stock = array_key_exists('stock', $data)
            ? (is_numeric($data['stock']) ? (float) $data['stock'] : null)
            : ($existing && is_numeric($existing->stock) ? (float) $existing->stock : null);

        $dailyConsumption = array_key_exists('daily_consumption', $data)
            ? (is_numeric($data['daily_consumption']) ? (float) $data['daily_consumption'] : null)
            : ($existing && is_numeric($existing->daily_consumption) ? (float) $existing->daily_consumption : null);

        if ($dailyConsumption !== null && $dailyConsumption > 0 && $stock !== null && $stock >= 0) {
            $data['coverage_days'] = round($stock / $dailyConsumption, 2);

            return $data;
        }

        $data['coverage_days'] = null;

        return $data;
    }

    private function normalizeAndValidateInventory(array $data): array
    {
        if (array_key_exists('reorder_level', $data) && ! array_key_exists('reorder_point', $data)) {
            $data['reorder_point'] = $data['reorder_level'];
        }

        if (array_key_exists('reorder_point', $data) && ! array_key_exists('reorder_level', $data)) {
            $data['reorder_level'] = $data['reorder_point'];
        }

        $stockMin = $data['stock_min'] ?? null;
        $stockMax = $data['stock_max'] ?? null;
        $reorderPoint = $data['reorder_point'] ?? null;

        if ($stockMin !== null && $stockMax !== null && $stockMax < $stockMin) {
            throw ValidationException::withMessages([
                'stock_max' => ['El stock maximo debe ser mayor o igual al stock minimo.'],
            ]);
        }

        if ($reorderPoint !== null && $stockMin !== null && $reorderPoint < $stockMin) {
            throw ValidationException::withMessages([
                'reorder_point' => ['El punto de reorden debe ser mayor o igual al stock minimo.'],
            ]);
        }

        if ($reorderPoint !== null && $stockMax !== null && $reorderPoint > $stockMax) {
            throw ValidationException::withMessages([
                'reorder_point' => ['El punto de reorden debe ser menor o igual al stock maximo.'],
            ]);
        }

        return $data;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::query()
            ->with('productType:id,name')
            ->orderBy('name');

        $term = trim((string) $request->query('q', ''));
        if ($term !== '') {
            $query->where(function ($builder) use ($term) {
                $builder
                    ->where('sku', 'like', "%{$term}%")
                    ->orWhere('name', 'like', "%{$term}%")
                    ->orWhere('presentation', 'like', "%{$term}%")
                    ->orWhere('unit', 'like', "%{$term}%");
            });
        }

        if ($request->boolean('paginate')) {
            $perPage = max(1, min((int) $request->query('per_page', 50), 200));
            $payload = $query->paginate($perPage)->appends($request->query());
            $rowsSignature = $this->rowsFingerprintSignature($payload->getCollection());

            $fingerprint = implode('|', [
                'products',
                'paged',
                (string) $payload->currentPage(),
                (string) $payload->perPage(),
                (string) $payload->total(),
                $rowsSignature,
                (string) $term,
            ]);

            return $this->catalogJsonResponse($request, $payload, $fingerprint, 'private, max-age=20, stale-while-revalidate=60');
        }

        $rows = $query->get();
        $rowsSignature = $this->rowsFingerprintSignature($rows);
        $fingerprint = implode('|', [
            'products',
            'full',
            (string) $rows->count(),
            $rowsSignature,
            (string) $term,
        ]);

        return $this->catalogJsonResponse($request, $rows, $fingerprint, 'private, max-age=20, stale-while-revalidate=60');
    }

    private function catalogJsonResponse(Request $request, mixed $payload, string $fingerprint, string $cacheControl): JsonResponse
    {
        $etag = 'W/"'.sha1($fingerprint).'"';
        $ifNoneMatch = trim((string) $request->header('If-None-Match', ''));

        if ($ifNoneMatch !== '' && $ifNoneMatch === $etag) {
            return response()
                ->json(null, 304)
                ->header('Cache-Control', $cacheControl)
                ->header('ETag', $etag);
        }

        $response = response()->json($payload);
        $response->headers->set('Cache-Control', $cacheControl);
        $response->headers->set('ETag', $etag);

        return $response;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'sku' => 'nullable|string|unique:products,sku',
            'product_type_id' => 'nullable|integer|exists:product_types,id',
            'name' => 'required|string|max:255',
            'presentation' => 'nullable|string|max:120',
            'image_url' => 'nullable|string|max:2048',
            'image_path' => 'nullable|string|max:2048',
            'unit' => 'required|string|max:50',
            'cost' => 'nullable|numeric|min:0',
            'stock' => 'nullable|numeric|min:0',
            'daily_consumption' => 'nullable|numeric|min:0',
            'initial_stock' => 'nullable|numeric|min:0',
            'stock_min' => 'nullable|numeric|min:0',
            'stock_max' => 'nullable|numeric|min:0',
            'reorder_level' => 'nullable|numeric|min:0',
            'reorder_point' => 'nullable|numeric|min:0',
        ]);
        $data = $this->normalizeImageReferences($data);
        $data = $this->normalizeAndValidateInventory($data);
        $data = $this->applyCoverageFromConsumption($data);
        $product = Product::create($data);

        return response()->json($product, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return response()->json($product);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product, CatalogImageCleanupService $catalogImageCleanup)
    {
        $previousImageUrl = $product->image_url;
        $previousManagedPath = $catalogImageCleanup->managedPathFromUrl($previousImageUrl);

        $data = $request->validate([
            'sku' => 'nullable|string|unique:products,sku,'.$product->id,
            'product_type_id' => 'nullable|integer|exists:product_types,id',
            'name' => 'sometimes|required|string|max:255',
            'presentation' => 'nullable|string|max:120',
            'image_url' => 'nullable|string|max:2048',
            'image_path' => 'nullable|string|max:2048',
            'unit' => 'sometimes|required|string|max:50',
            'cost' => 'nullable|numeric|min:0',
            'stock' => 'nullable|numeric|min:0',
            'daily_consumption' => 'nullable|numeric|min:0',
            'initial_stock' => 'nullable|numeric|min:0',
            'stock_min' => 'nullable|numeric|min:0',
            'stock_max' => 'nullable|numeric|min:0',
            'reorder_level' => 'nullable|numeric|min:0',
            'reorder_point' => 'nullable|numeric|min:0',
        ]);
        $data = $this->normalizeImageReferences($data);

        // Protect existing image when frontend sends an empty string by mistake.
        if (array_key_exists('image_url', $data) && trim((string) $data['image_url']) === '') {
            unset($data['image_url']);
        }

        $data = $this->normalizeAndValidateInventory($data);
        $data = $this->applyCoverageFromConsumption($data, $product);
        $product->update($data);

        $currentManagedPath = $catalogImageCleanup->managedPathFromUrl($product->image_url);
        $sameManagedFile = $previousManagedPath !== null
            && $currentManagedPath !== null
            && $previousManagedPath === $currentManagedPath;

        if (array_key_exists('image_url', $data) && $previousImageUrl !== $product->image_url && ! $sameManagedFile) {
            $catalogImageCleanup->deleteIfOrphaned($previousImageUrl, $product->id, null);
        }

        return response()->json($product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product, CatalogImageCleanupService $catalogImageCleanup)
    {
        $deletedImageUrl = $product->image_url;
        $deletedProductId = $product->id;

        $product->delete();

        $catalogImageCleanup->deleteIfOrphaned($deletedImageUrl, $deletedProductId, null);

        return response()->json(null, 204);
    }
}
