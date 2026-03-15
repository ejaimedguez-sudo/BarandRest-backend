<?php

namespace App\Console\Commands;

use App\Models\Commission;
use App\Models\Order;
use Illuminate\Console\Command;

class ComputeCommissions extends Command
{
    protected $signature = 'commissions:compute {from} {to} {percent=5}';

    protected $description = 'Compute commissions for orders in a date range (from,to) and store them.';

    public function handle()
    {
        $from = $this->argument('from');
        $to = $this->argument('to');
        $percent = (float) $this->argument('percent');

        $orders = Order::whereBetween('created_at', [$from, $to])->get();
        $count = 0;
        foreach ($orders as $order) {
            if (! $order->waiter_id) {
                continue;
            }
            $amount = ($order->total ?? 0) * ($percent / 100.0);
            Commission::create([
                'user_id' => $order->waiter_id,
                'order_id' => $order->id,
                'amount' => round($amount, 2),
                'percent' => $percent,
            ]);
            $count++;
        }

        $this->info("Created $count commissions for period $from to $to");

        return 0;
    }
}
