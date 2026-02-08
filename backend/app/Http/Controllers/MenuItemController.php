<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use Illuminate\Http\Request;

class MenuItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(\App\Models\MenuItem::all());
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
            'code' => 'nullable|string|unique:menu_items,code',
            'name' => 'required|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'cost' => 'numeric',
            'category' => 'nullable|string',
        ]);
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
    public function update(Request $request, MenuItem $menuItem)
    {
        $data = $request->validate([
            'code' => 'nullable|string|unique:menu_items,code,' . $menuItem->id,
            'name' => 'sometimes|required|string',
            'description' => 'nullable|string',
            'price' => 'numeric',
            'cost' => 'numeric',
            'category' => 'nullable|string',
        ]);
        $menuItem->update($data);
        return response()->json($menuItem);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MenuItem $menuItem)
    {
        $menuItem->delete();
        return response()->json(null, 204);
    }
}
