<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class GenerateDailyReportCsv implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $date;
    public $groupBy;

    public function __construct(string $date, ?string $groupBy = null)
    {
        $this->date = $date;
        $this->groupBy = $groupBy;
    }

    public function handle()
    {
        $date = $this->date;
        $groupBy = $this->groupBy;

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

        $filename = 'daily-' . $date . ($groupBy ? "-{$groupBy}" : '') . '.csv';
        $path = storage_path('app/reports/' . $filename);
        @mkdir(dirname($path), 0755, true);
        $fp = fopen($path, 'w');
        if ($fp) {
            $headers = array_keys((array)($rows->first() ?? ['sales'=>0,'cost'=>0,'qty'=>0,'orders'=>0]));
            fputcsv($fp, $headers);
            foreach ($rows as $r) {
                fputcsv($fp, array_values((array)$r));
            }
            fclose($fp);
        }
        return $filename;
    }
}
