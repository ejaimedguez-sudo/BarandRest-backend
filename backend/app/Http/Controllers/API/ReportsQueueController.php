<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Jobs\QueueGenerateAndEmailDailyReport;
use Illuminate\Http\Request;

class ReportsQueueController extends Controller
{
    public function queueDaily(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'group_by' => 'nullable|string',
            'email' => 'required|email',
        ]);

        QueueGenerateAndEmailDailyReport::dispatch($data['date'], $data['group_by'] ?? null, $data['email']);

        return response()->json(['status' => 'queued']);
    }
}
