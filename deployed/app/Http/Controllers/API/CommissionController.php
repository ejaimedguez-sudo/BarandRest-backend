<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Commission;
use App\Models\Order;

class CommissionController extends Controller
{
    /**
     * Compute commissions for a given period and save to commissions table.
     * Request: { from, to, percent }
     */
    public function compute(Request $request)
    {
        $data = $request->validate([
            'from' => 'required|date',
            'to' => 'required|date',
            'percent' => 'required|numeric|min:0',
        ]);

        $orders = Order::whereBetween('created_at', [$data['from'], $data['to']])->get();
        $created = 0;
        foreach ($orders as $order) {
            if (!$order->waiter_id) continue;
            $amount = ($order->total ?? 0) * ($data['percent'] / 100.0);
            Commission::create([
                'user_id' => $order->waiter_id,
                'order_id' => $order->id,
                'amount' => round($amount, 2),
                'percent' => $data['percent'],
            ]);
            $created++;
        }

        return response()->json(['created' => $created]);
    }
}
