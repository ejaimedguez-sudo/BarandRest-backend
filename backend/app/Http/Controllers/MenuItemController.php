<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\MenuCategory;
use App\Services\CatalogImageCleanupService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class MenuItemController extends Controller
{
    private function getRequiredMarginForCategory(?int $menuCategoryId): float
    {
        $default = (float) config('profitability.menu_item_min_margin_default', 0);
        if (!$menuCategoryId) {
            return $default;
        }

        $category = MenuCategory::query()->select(['id', 'code'])->find($menuCategoryId);
        if (!$category || empty($category->code)) {
            return $default;
        }

        $byCategory = (array) config('profitability.menu_item_min_margin_by_category', []);
        if (!array_key_exists($category->code, $byCategory)) {
            return $default;
        }

        return (float) $byCategory[$category->code];
    }

    private function enforceMinimumMargin(Request $request, array $data, ?MenuItem $existing = null): void
    {
        $role = strtolower((string) $request->header('X-USER-ROLE', ''));
        $overrideRoles = array_map('strtolower', (array) config('profitability.margin_override_roles', []));
        if ($role !== '' && in_array($role, $overrideRoles, true)) {
            return;
        }

        $categoryId = array_key_exists('menu_category_id', $data)
            ? $data['menu_category_id']
            : ($existing?->menu_category_id);
        $requiredMargin = $this->getRequiredMarginForCategory($categoryId ? (int) $categoryId : null);
        if ($requiredMargin <= 0) {
            return;
        }

        $margin = null;
        if (array_key_exists('profit_margin_percent', $data) && is_numeric($data['profit_margin_percent'])) {
            $margin = (float) $data['profit_margin_percent'];
        }

        if ($margin === null) {
            $price = array_key_exists('price', $data) ? (is_numeric($data['price']) ? (float) $data['price'] : null) : ($existing ? (float) $existing->price : null);
            $cost = array_key_exists('cost', $data) ? (is_numeric($data['cost']) ? (float) $data['cost'] : null) : ($existing && is_numeric($existing->cost) ? (float) $existing->cost : null);
            if ($price !== null && $price > 0 && $cost !== null) {
                $margin = (($price - $cost) / $price) * 100;
            }
        }

        if ($margin !== null && $margin < $requiredMargin) {
            throw ValidationException::withMessages([
                'profit_margin_percent' => [
                    sprintf('El margen minimo requerido para esta categoria es %.2f%%.', $requiredMargin),
                ],
            ]);
        }
    }

    private function resolveProfitability(array $data): array
    {
        $price = array_key_exists('price', $data) ? (is_numeric($data['price']) ? (float) $data['price'] : null) : null;
        $cost = array_key_exists('cost', $data) ? (is_numeric($data['cost']) ? (float) $data['cost'] : null) : null;
        $manualCost = array_key_exists('manual_cost', $data) ? (is_numeric($data['manual_cost']) ? (float) $data['manual_cost'] : null) : null;
        $isRecipe = array_key_exists('is_recipe', $data) ? (bool) $data['is_recipe'] : null;

        // For non-recipe items, mirror manual cost into operational cost if explicit cost was not sent.
        if ($manualCost !== null && $manualCost >= 0 && ($isRecipe === false || $isRecipe === null) && !array_key_exists('cost', $data)) {
            $data['cost'] = round($manualCost, 2);
            $cost = $data['cost'];
        }

        if (array_key_exists('profit_margin_percent', $data) && $data['profit_margin_percent'] !== null) {
            $data['profit_margin_percent'] = round((float) $data['profit_margin_percent'], 2);
            return $data;
        }

        if ($price !== null && $price > 0 && $cost !== null && !array_key_exists('profit_margin_percent', $data)) {
            $margin = (($price - $cost) / $price) * 100;
            $data['profit_margin_percent'] = round($margin, 2);
        }

        return $data;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = \App\Models\MenuItem::query()
            ->with(['productType:id,name', 'menuCategory:id,name'])
            ->orderBy('name');

        $term = trim((string) $request->query('q', ''));
        if ($term !== '') {
            $query->where(function ($builder) use ($term) {
                $builder
                    ->where('code', 'like', "%{$term}%")
                    ->orWhere('name', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%")
                    ->orWhere('dish', 'like', "%{$term}%")
                    ->orWhere('kitchen', 'like', "%{$term}%");
            });
        }

        if ($request->boolean('paginate')) {
            $perPage = max(1, min((int) $request->query('per_page', 50), 200));
            $payload = $query->paginate($perPage)->appends($request->query());

            $fingerprint = implode('|', [
                'menu-items',
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
            'menu-items',
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
            'code' => ['nullable', 'string', 'max:100', 'regex:/^[A-Za-z0-9\-_.]+$/', 'unique:menu_items,code'],
            'name' => 'required|string|max:255',
            'product_type_id' => 'nullable|integer|exists:product_types,id',
            'menu_category_id' => 'nullable|integer|exists:menu_categories,id',
            'description' => 'nullable|string|max:1000',
            'image_url' => 'nullable|string|max:2048',
            'prep_time_minutes' => 'nullable|integer|min:0|max:1440',
            'dish' => 'nullable|string|max:120',
            'kitchen' => 'nullable|string|max:120',
            'servings' => 'nullable|integer|min:0|max:1000',
            'calories' => 'nullable|integer|min:0|max:100000',
            'equipment' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'manual_cost' => 'nullable|numeric|min:0',
            'profit_margin_percent' => 'nullable|numeric|min:-999.99|max:99.99',
            'category' => 'nullable|string|max:120',
            'is_recipe' => 'nullable|boolean',
        ], [
            'code.regex' => 'El codigo solo puede contener letras, numeros, guion (-), guion bajo (_) y punto (.)',
            'code.unique' => 'El codigo ya existe. Usa un codigo diferente.',
            'price.min' => 'El precio no puede ser menor a 0.',
            'cost.min' => 'El costo no puede ser menor a 0.',
            'profit_margin_percent.max' => 'El margen no puede ser mayor a 99.99%.',
        ]);

        if (!empty($data['menu_category_id']) && !array_key_exists('category', $data)) {
            $category = MenuCategory::query()->find($data['menu_category_id']);
            if ($category) {
                $data['category'] = $category->name;
            }
        }

        $data = $this->resolveProfitability($data);
        $this->enforceMinimumMargin($request, $data);

        $item = \App\Models\MenuItem::create($data);
        return response()->json($item, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(MenuItem $menuItem)
    {
        return response()->json($menuItem);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MenuItem $menuItem)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MenuItem $menuItem, CatalogImageCleanupService $catalogImageCleanup)
    {
        $previousImageUrl = $menuItem->image_url;

        $data = $request->validate([
            'code' => ['nullable', 'string', 'max:100', 'regex:/^[A-Za-z0-9\-_.]+$/', 'unique:menu_items,code,' . $menuItem->id],
            'name' => 'sometimes|required|string|max:255',
            'product_type_id' => 'nullable|integer|exists:product_types,id',
            'menu_category_id' => 'nullable|integer|exists:menu_categories,id',
            'description' => 'nullable|string|max:1000',
            'image_url' => 'nullable|string|max:2048',
            'prep_time_minutes' => 'nullable|integer|min:0|max:1440',
            'dish' => 'nullable|string|max:120',
            'kitchen' => 'nullable|string|max:120',
            'servings' => 'nullable|integer|min:0|max:1000',
            'calories' => 'nullable|integer|min:0|max:100000',
            'equipment' => 'nullable|string|max:255',
            'price' => 'numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'manual_cost' => 'nullable|numeric|min:0',
            'profit_margin_percent' => 'nullable|numeric|min:-999.99|max:99.99',
            'category' => 'nullable|string|max:120',
            'is_recipe' => 'nullable|boolean',
        ], [
            'code.regex' => 'El codigo solo puede contener letras, numeros, guion (-), guion bajo (_) y punto (.)',
            'code.unique' => 'El codigo ya existe. Usa un codigo diferente.',
            'price.min' => 'El precio no puede ser menor a 0.',
            'cost.min' => 'El costo no puede ser menor a 0.',
            'profit_margin_percent.max' => 'El margen no puede ser mayor a 99.99%.',
        ]);

        if (!empty($data['menu_category_id']) && !array_key_exists('category', $data)) {
            $category = MenuCategory::query()->find($data['menu_category_id']);
            if ($category) {
                $data['category'] = $category->name;
            }
        }

        $data = $this->resolveProfitability($data);
        $this->enforceMinimumMargin($request, $data, $menuItem);

        $menuItem->update($data);

        if (array_key_exists('image_url', $data) && $previousImageUrl !== $menuItem->image_url) {
            $catalogImageCleanup->deleteIfOrphaned($previousImageUrl, null, $menuItem->id);
        }

        return response()->json($menuItem);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MenuItem $menuItem, CatalogImageCleanupService $catalogImageCleanup)
    {
        $deletedImageUrl = $menuItem->image_url;
        $deletedMenuItemId = $menuItem->id;

        $menuItem->delete();

        $catalogImageCleanup->deleteIfOrphaned($deletedImageUrl, null, $deletedMenuItemId);

        return response()->json(null, 204);
    }
}
