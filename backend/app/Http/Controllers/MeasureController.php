<?php

namespace App\Http\Controllers;

use App\Models\Measure;
use Illuminate\Http\Request;

class MeasureController extends Controller
{
    public function index()
    {
        return response()->json(
            Measure::query()
                ->orderBy('name')
                ->get()
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120|unique:measures,name',
            'abbreviation' => 'nullable|string|max:20|unique:measures,abbreviation',
            'description' => 'nullable|string|max:500',
        ]);

        $measure = Measure::create($data);

        return response()->json($measure, 201);
    }

    public function show(Measure $measure)
    {
        return response()->json($measure);
    }

    public function update(Request $request, Measure $measure)
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:120|unique:measures,name,' . $measure->id,
            'abbreviation' => 'nullable|string|max:20|unique:measures,abbreviation,' . $measure->id,
            'description' => 'nullable|string|max:500',
        ]);

        $measure->update($data);

        return response()->json($measure);
    }

    public function destroy(Measure $measure)
    {
        $measure->delete();

        return response()->json(null, 204);
    }
}
