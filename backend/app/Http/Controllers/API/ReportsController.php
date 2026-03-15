<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function daily(Request $request)
    {
        $date = $request->query('date', date('Y-m-d'));
        $groupBy = $request->query('group_by'); // table, waiter, category, menu_item

        $base = DB::table('order_items as oi')
            ->join('orders as o', 'oi.order_id', '=', 'o.id')
            ->join('menu_items as mi', 'oi.menu_item_id', '=', 'mi.id')
            ->selectRaw('SUM(oi.quantity * oi.unit_price) as sales, SUM(oi.quantity * oi.cost) as cost, SUM(oi.quantity) as qty, COUNT(DISTINCT o.id) as orders')
            ->whereDate('o.created_at', $date);

        if ($groupBy === 'waiter') {
            $rows = $base->addSelect('o.waiter_id')->groupBy('o.waiter_id')->get();
        } elseif ($groupBy === 'table') {
            $rows = $base->addSelect('o.table_id')->groupBy('o.table_id')->get();
        } elseif ($groupBy === 'category') {
            $rows = $base->addSelect('mi.category')->groupBy('mi.category')->get();
        } elseif ($groupBy === 'menu_item') {
            $rows = $base->addSelect('mi.id as menu_item_id', 'mi.name as menu_item')->groupBy('mi.id')->get();
        } else {
            $rows = $base->get();
        }

        // support CSV export (synchronous for small datasets)
        $format = $request->query('format');
        if ($format === 'csv') {
            $filename = 'daily-'.$date.($groupBy ? "-{$groupBy}" : '').'.csv';
            $path = storage_path('app/reports/'.$filename);
            @mkdir(dirname($path), 0755, true);
            $fp = fopen($path, 'w');
            if ($fp) {
                // headers
                $headers = array_keys((array) ($rows->first() ?? ['sales' => 0, 'cost' => 0, 'qty' => 0, 'orders' => 0]));
                fputcsv($fp, $headers);
                foreach ($rows as $r) {
                    fputcsv($fp, array_values((array) $r));
                }
                fclose($fp);

                return response()->json(['csv' => url('/api/reports/download/'.$filename)]);
            }

            return response()->json(['error' => 'Could not generate CSV'], 500);
        }

        return response()->json($rows);
    }

    public function sales(Request $request)
    {
        $from = $request->query('from');
        $to = $request->query('to');
        $q = DB::table('orders')->selectRaw('SUM(total) as sales, COUNT(*) as orders');
        if ($from) {
            $q->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $q->whereDate('created_at', '<=', $to);
        }

        return response()->json($q->first());
    }

    public function weekly(Request $request)
    {
        $start = $request->query('start');
        $end = $request->query('end');
        $q = DB::table('orders')
            ->selectRaw('DATE(created_at) as day, SUM(total) as sales, COUNT(*) as orders')
            ->groupByRaw('DATE(created_at)')
            ->orderBy('day');
        if ($start) {
            $q->whereDate('created_at', '>=', $start);
        }
        if ($end) {
            $q->whereDate('created_at', '<=', $end);
        }

        return response()->json($q->get());
    }

    public function monthly(Request $request)
    {
        $year = $request->query('year', date('Y'));
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            $monthExpression = "CAST(strftime('%m', created_at) AS INTEGER)";
            $q = DB::table('orders')
                ->selectRaw("{$monthExpression} as month, SUM(total) as sales, COUNT(*) as orders")
                ->whereRaw("CAST(strftime('%Y', created_at) AS INTEGER) = ?", [(int) $year])
                ->groupByRaw($monthExpression)
                ->orderBy('month');

            return response()->json($q->get());
        }

        if ($driver === 'pgsql') {
            $monthExpression = 'EXTRACT(MONTH FROM created_at)';
            $q = DB::table('orders')
                ->selectRaw("{$monthExpression} as month, SUM(total) as sales, COUNT(*) as orders")
                ->whereRaw('EXTRACT(YEAR FROM created_at) = ?', [(int) $year])
                ->groupByRaw($monthExpression)
                ->orderBy('month');

            return response()->json($q->get());
        }

        $q = DB::table('orders')
            ->selectRaw('MONTH(created_at) as month, SUM(total) as sales, COUNT(*) as orders')
            ->whereYear('created_at', $year)
            ->groupByRaw('MONTH(created_at)')
            ->orderBy('month');

        return response()->json($q->get());
    }

    public function yearly(Request $request)
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            $yearExpression = "CAST(strftime('%Y', created_at) AS INTEGER)";
            $q = DB::table('orders')
                ->selectRaw("{$yearExpression} as year, SUM(total) as sales, COUNT(*) as orders")
                ->groupByRaw($yearExpression)
                ->orderBy('year');

            return response()->json($q->get());
        }

        if ($driver === 'pgsql') {
            $yearExpression = 'EXTRACT(YEAR FROM created_at)';
            $q = DB::table('orders')
                ->selectRaw("{$yearExpression} as year, SUM(total) as sales, COUNT(*) as orders")
                ->groupByRaw($yearExpression)
                ->orderBy('year');

            return response()->json($q->get());
        }

        $q = DB::table('orders')
            ->selectRaw('YEAR(created_at) as year, SUM(total) as sales, COUNT(*) as orders')
            ->groupByRaw('YEAR(created_at)')
            ->orderBy('year');

        return response()->json($q->get());
    }
}
