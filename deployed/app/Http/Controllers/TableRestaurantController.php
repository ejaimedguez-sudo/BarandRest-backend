<?php

namespace App\Http\Controllers;

use App\Models\TableRestaurant;
use Illuminate\Http\Request;

class TableRestaurantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(\App\Models\TableRestaurant::all());
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
            'name' => 'required|string',
            'capacity' => 'integer',
            'location' => 'nullable|string',
        ]);
        $t = \App\Models\TableRestaurant::create($data);
        return response()->json($t, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(TableRestaurant $tableRestaurant)
    {
        return response()->json($tableRestaurant);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TableRestaurant $tableRestaurant)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TableRestaurant $tableRestaurant)
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string',
            'capacity' => 'integer',
            'location' => 'nullable|string',
        ]);
        $tableRestaurant->update($data);
        return response()->json($tableRestaurant);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TableRestaurant $tableRestaurant)
    {
        $tableRestaurant->delete();
        return response()->json(null, 204);
    }
}
