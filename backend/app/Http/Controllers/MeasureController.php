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
    public function index()
    {
        if ($error = $this->ensureMeasuresTableReady()) {
            return $error;
        }

        return response()->json(
            Measure::query()
                ->orderBy('name')
                ->get()
        );
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
