<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CatalogImageCleanupService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
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
        if (array_key_exists('reorder_level', $data) && !array_key_exists('reorder_point', $data)) {
            $data['reorder_point'] = $data['reorder_level'];
        }

        if (array_key_exists('reorder_point', $data) && !array_key_exists('reorder_level', $data)) {
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

            $fingerprint = implode('|', [
                'products',
                'paged',
                (string) $payload->currentPage(),
                (string) $payload->perPage(),
                (string) $payload->total(),
                (string) optional($payload->getCollection()->max('updated_at'))->timestamp,
                (string) $term,
            ]);

            return $this->catalogJsonResponse($request, $payload, $fingerprint, 'private, max-age=20, stale-while-revalidate=60');
        }

        $rows = $query->get();
        $fingerprint = implode('|', [
            'products',
            'full',
            (string) $rows->count(),
            (string) optional($rows->max('updated_at'))->timestamp,
            (string) $term,
        ]);

        return $this->catalogJsonResponse($request, $rows, $fingerprint, 'private, max-age=20, stale-while-revalidate=60');
    }

    private function catalogJsonResponse(Request $request, mixed $payload, string $fingerprint, string $cacheControl): JsonResponse
    {
        $etag = 'W/"' . sha1($fingerprint) . '"';
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

        $data = $request->validate([
            'sku' => 'nullable|string|unique:products,sku,' . $product->id,
            'product_type_id' => 'nullable|integer|exists:product_types,id',
            'name' => 'sometimes|required|string|max:255',
            'presentation' => 'nullable|string|max:120',
            'image_url' => 'nullable|string|max:2048',
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
        $data = $this->normalizeAndValidateInventory($data);
        $data = $this->applyCoverageFromConsumption($data, $product);
        $product->update($data);

        if (array_key_exists('image_url', $data) && $previousImageUrl !== $product->image_url) {
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
