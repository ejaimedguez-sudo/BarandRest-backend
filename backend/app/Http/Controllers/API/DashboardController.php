<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    /**
     * Return basic metrics for dashboard: daily sales, weekly sales, top products, low stock alerts.
     */
    public function metrics(Request $request)
    {
        $cacheKey = 'dashboard_metrics_v1';

        $metrics = Cache::remember($cacheKey, now()->addMinutes(5), function () {
            $today = now()->startOfDay();
            $weekAgo = now()->subDays(7)->startOfDay();

            $dailySales = DB::table('orders')
                ->whereDate('created_at', '>=', $today)
                ->where('status', 'closed')
                ->sum('total');

            $weeklySales = DB::table('orders')
                ->where('created_at', '>=', $weekAgo)
                ->where('status', 'closed')
                ->select(DB::raw('DATE(created_at) as day'), DB::raw('SUM(total) as total'))
                ->groupBy('day')
                ->orderBy('day')
                ->get();

            $topProducts = DB::table('order_items')
                ->join('menu_items', 'menu_items.id', '=', 'order_items.menu_item_id')
                ->select('menu_items.name', DB::raw('SUM(order_items.quantity) as qty'))
                ->groupBy('menu_items.name')
                ->orderByDesc('qty')
                ->limit(10)
                ->get();

            $lowStock = DB::table('products')
                ->whereRaw('stock <= COALESCE(reorder_point, reorder_level)')
                ->select('id', 'name', 'stock', DB::raw('COALESCE(reorder_point, reorder_level) as reorder_point'))
                ->limit(10)
                ->get();

            return compact('dailySales', 'weeklySales', 'topProducts', 'lowStock');
        });

        return response()->json($metrics);
    }

    /**
     * Clear dashboard cache (admin action)
     */
    public function clearCache()
    {
        Cache::forget('dashboard_metrics_v1');
        return response()->json(['ok' => true]);
    }
}
