<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(\App\Models\Order::with('orderItems')->get());
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
            'table_id' => 'nullable|integer|exists:tables_restaurant,id',
            'customer_id' => 'nullable|integer|exists:customers,id',
            'waiter_id' => 'nullable|integer|exists:users,id',
            'status' => 'nullable|in:open,closed,cancelled',
            'order_items' => 'required|array|min:1',
            'order_items.*.menu_item_id' => 'required|integer|exists:menu_items,id',
            'order_items.*.quantity' => 'required|numeric|min:1',
            'order_items.*.price' => 'nullable|numeric|min:0',
            'order_items.*.unit_price' => 'nullable|numeric|min:0',
        ]);

        $order = null;
        DB::beginTransaction();
        try {
            $orderData = [
                'table_id' => $data['table_id'] ?? null,
                'customer_id' => $data['customer_id'] ?? null,
                'waiter_id' => $data['waiter_id'] ?? null,
                'status' => $data['status'] ?? 'open',
                'total' => 0,
            ];
            $order = Order::create($orderData);

            $total = 0.0;
            foreach ($data['order_items'] as $item) {
                $menuItem = MenuItem::find($item['menu_item_id']);
                if (! $menuItem) {
                    continue;
                }
                $qty = is_numeric($item['quantity']) ? (float) $item['quantity'] : 0.0;

                $unitCost = $menuItem->calculateCostFromIngredients();
                $unitPrice = isset($item['unit_price'])
                    ? (float) $item['unit_price']
                    : (isset($item['price']) ? (float) $item['price'] : ($menuItem->price ?? $menuItem->suggestPrice(30) ?? 0.0));

                $lineTotal = $unitPrice * $qty;
                $total += $lineTotal;

                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $menuItem->id,
                    'quantity' => $qty,
                    'price' => $unitPrice,
                    'cost' => $unitCost,
                ]);

                // Decrement stock for each ingredient proportional to quantity
                $menuItem->loadMissing('ingredients.product');
                foreach ($menuItem->ingredients as $ing) {
                    $product = $ing->product;
                    if (! $product) {
                        continue;
                    }
                    $used = (is_numeric($ing->quantity) ? (float) $ing->quantity : 0.0) * $qty;
                    $product->stock = max(0, ($product->stock ?? 0) - $used);
                    $product->save();

                    // record stock movement
                    StockMovement::create([
                        'product_id' => $product->id,
                        'quantity' => -1 * $used,
                        'type' => 'out',
                        'notes' => 'order#'.$order->id,
                        'created_by' => $order->waiter_id ?? null,
                    ]);
                }
            }

            $order->total = round($total, 2);
            $order->save();

            DB::commit();
            $order->load('orderItems');

            return response()->json($order, 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['error' => 'Could not create order', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        $order->load('orderItems');

        return response()->json($order);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => 'nullable|string',
            'total' => 'numeric',
        ]);
        $order->update($data);

        return response()->json($order);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        $order->delete();

        return response()->json(null, 204);
    }
}
