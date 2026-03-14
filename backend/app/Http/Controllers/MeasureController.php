<?php

namespace App\Http\Controllers;

use App\Models\Measure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Throwable;

class MeasureController extends Controller
{
    public function index(Request $request)
    {
        if ($error = $this->ensureMeasuresTableReady()) {
            return $error;
        }

        $query = Measure::query()->orderBy('name');

        $term = trim((string) $request->query('q', ''));
        if ($term !== '') {
            $query->where(function ($builder) use ($term) {
                $builder
                    ->where('name', 'like', "%{$term}%")
                    ->orWhere('abbreviation', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%");
            });
        }

        if ($request->boolean('paginate')) {
            $perPage = max(1, min((int) $request->query('per_page', 50), 200));
            $payload = $query->paginate($perPage)->appends($request->query());
            $fingerprint = implode('|', [
                'measures',
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
            'measures',
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

    public function store(Request $request)
    {
        if ($error = $this->ensureMeasuresTableReady()) {
            return $error;
        }

        $data = $request->validate([
            'name' => 'required|string|max:120|unique:measures,name',
            'abbreviation' => 'nullable|string|max:20|unique:measures,abbreviation',
            'description' => 'nullable|string|max:500',
        ]);

        $measure = Measure::create($data);

        return response()->json($measure, 201);
    }

    public function show(int $measure)
    {
        if ($error = $this->ensureMeasuresTableReady()) {
            return $error;
        }

        return response()->json(Measure::query()->findOrFail($measure));
    }

    public function update(Request $request, int $measure)
    {
        if ($error = $this->ensureMeasuresTableReady()) {
            return $error;
        }

        $measureModel = Measure::query()->findOrFail($measure);

        $data = $request->validate([
            'name' => 'sometimes|required|string|max:120|unique:measures,name,' . $measureModel->id,
            'abbreviation' => 'nullable|string|max:20|unique:measures,abbreviation,' . $measureModel->id,
            'description' => 'nullable|string|max:500',
        ]);

        $measureModel->update($data);

        return response()->json($measureModel);
    }

    public function destroy(int $measure)
    {
        if ($error = $this->ensureMeasuresTableReady()) {
            return $error;
        }

        $measureModel = Measure::query()->findOrFail($measure);
        $measureModel->delete();

        return response()->json(null, 204);
    }

    private function ensureMeasuresTableReady(): ?JsonResponse
    {
        if (Schema::hasTable('measures')) {
            return null;
        }

        try {
            Artisan::call('migrate', ['--force' => true]);
        } catch (Throwable $error) {
            Log::error('No se pudo ejecutar migracion automatica para catalogo de medidas.', [
                'exception' => $error,
            ]);

            return response()->json([
                'message' => 'No se pudo preparar automaticamente el catalogo de medidas. Ejecuta php artisan migrate --force.',
            ], 503);
        }

        if (!Schema::hasTable('measures')) {
            return response()->json([
                'message' => 'El catalogo de medidas no esta disponible aun. Ejecuta php artisan migrate --force.',
            ], 503);
        }

        return null;
    }
}
