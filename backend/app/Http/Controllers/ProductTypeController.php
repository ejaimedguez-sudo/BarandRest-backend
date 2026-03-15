<?php

namespace App\Http\Controllers;

use App\Models\ProductType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductTypeController extends Controller
{
    public function index(Request $request)
    {
        $query = ProductType::query()->orderBy('name');

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
                'product-types',
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
            'product-types',
            'full',
            (string) $rows->count(),
            (string) optional($rows->max('updated_at'))->timestamp,
            (string) $term,
        ]);

        return $this->catalogJsonResponse($request, $rows, $fingerprint, 'public, max-age=300, stale-while-revalidate=600');
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

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => ['nullable', 'string', 'max:80', 'regex:/^[A-Za-z0-9\-_.]+$/', 'unique:product_types,code'],
            'name' => 'required|string|max:120|unique:product_types,name',
            'description' => 'nullable|string|max:1000',
        ], [
            'code.regex' => 'El codigo solo puede contener letras, numeros, guion (-), guion bajo (_) y punto (.)',
            'code.unique' => 'El codigo ya existe. Usa un codigo diferente.',
            'name.unique' => 'El nombre del tipo ya existe. Usa un nombre diferente.',
        ]);

        $type = ProductType::create($data);

        return response()->json($type, 201);
    }

    public function show(ProductType $productType)
    {
        return response()->json($productType);
    }

    public function edit(ProductType $productType)
    {
        //
    }

    public function update(Request $request, ProductType $productType)
    {
        $data = $request->validate([
            'code' => ['nullable', 'string', 'max:80', 'regex:/^[A-Za-z0-9\-_.]+$/', 'unique:product_types,code,'.$productType->id],
            'name' => 'sometimes|required|string|max:120|unique:product_types,name,'.$productType->id,
            'description' => 'nullable|string|max:1000',
        ], [
            'code.regex' => 'El codigo solo puede contener letras, numeros, guion (-), guion bajo (_) y punto (.)',
            'code.unique' => 'El codigo ya existe. Usa un codigo diferente.',
            'name.unique' => 'El nombre del tipo ya existe. Usa un nombre diferente.',
        ]);

        $productType->update($data);

        return response()->json($productType);
    }

    public function destroy(ProductType $productType)
    {
        $productType->delete();

        return response()->json(null, 204);
    }
}
