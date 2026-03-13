<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(
            Product::query()
                ->orderBy('name')
                ->get()
        );
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
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'cost' => 'nullable|numeric|min:0',
            'stock' => 'nullable|numeric',
            'reorder_level' => 'nullable|integer|min:0',
        ]);
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
    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'sku' => 'nullable|string|unique:products,sku,' . $product->id,
            'name' => 'sometimes|required|string|max:255',
            'unit' => 'sometimes|required|string|max:50',
            'cost' => 'nullable|numeric|min:0',
            'stock' => 'nullable|numeric',
            'reorder_level' => 'nullable|integer|min:0',
        ]);
        $product->update($data);
        return response()->json($product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(null, 204);
    }
}
