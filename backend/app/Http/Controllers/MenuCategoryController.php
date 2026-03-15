<?php

namespace App\Http\Controllers;

use App\Models\MenuCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MenuCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = MenuCategory::query()
            ->orderByRaw('COALESCE(sort_order, 999999) asc')
            ->orderBy('name');

        $term = trim((string) $request->query('q', ''));
        if ($term !== '') {
            $query->where(function ($builder) use ($term) {
                $builder
                    ->where('code', 'like', "%{$term}%")
                    ->orWhere('name', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%");
            });
        }

        if ($request->boolean('paginate')) {
            $perPage = max(1, min((int) $request->query('per_page', 50), 200));
            $payload = $query->paginate($perPage)->appends($request->query());
            $fingerprint = implode('|', [
                'menu-categories',
                'paged',
                (string) $payload->currentPage(),
                (string) $payload->perPage(),
                (string) $payload->total(),
                (string) optional($payload->getCollection()->max('updated_at'))->timestamp,
                (string) $term,
            ]);

            return $this->catalogJsonResponse($request, $payload, $fingerprint, 'public, max-age=300, stale-while-revalidate=600');
        }

        $rows = $query->get();
        $fingerprint = implode('|', [
            'menu-categories',
            'full',
            (string) $rows->count(),
            (string) optional($rows->max('updated_at'))->timestamp,
            (string) $term,
        ]);

        return $this->catalogJsonResponse($request, $rows, $fingerprint, 'public, max-age=300, stale-while-revalidate=600');
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

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => ['nullable', 'string', 'max:80', 'regex:/^[A-Za-z0-9\-_.]+$/', 'unique:menu_categories,code'],
            'name' => 'required|string|max:120|unique:menu_categories,name',
            'description' => 'nullable|string|max:1000',
            'sort_order' => 'nullable|integer|min:0|max:1000000',
        ], [
            'code.regex' => 'El codigo solo puede contener letras, numeros, guion (-), guion bajo (_) y punto (.)',
            'code.unique' => 'El codigo ya existe. Usa un codigo diferente.',
            'name.unique' => 'El nombre de la categoria ya existe. Usa un nombre diferente.',
        ]);

        $category = MenuCategory::create($data);
        return response()->json($category, 201);
    }

    public function show(MenuCategory $menuCategory)
    {
        return response()->json($menuCategory);
    }

    public function edit(MenuCategory $menuCategory)
    {
        //
    }

    public function update(Request $request, MenuCategory $menuCategory)
    {
        $data = $request->validate([
            'code' => ['nullable', 'string', 'max:80', 'regex:/^[A-Za-z0-9\-_.]+$/', 'unique:menu_categories,code,' . $menuCategory->id],
            'name' => 'sometimes|required|string|max:120|unique:menu_categories,name,' . $menuCategory->id,
            'description' => 'nullable|string|max:1000',
            'sort_order' => 'nullable|integer|min:0|max:1000000',
        ], [
            'code.regex' => 'El codigo solo puede contener letras, numeros, guion (-), guion bajo (_) y punto (.)',
            'code.unique' => 'El codigo ya existe. Usa un codigo diferente.',
            'name.unique' => 'El nombre de la categoria ya existe. Usa un nombre diferente.',
        ]);

        $menuCategory->update($data);
        return response()->json($menuCategory);
    }

    public function destroy(MenuCategory $menuCategory)
    {
        $menuCategory->delete();
        return response()->json(null, 204);
    }
}
