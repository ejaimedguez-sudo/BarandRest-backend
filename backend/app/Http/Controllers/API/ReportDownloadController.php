<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportDownloadController extends Controller
{
    public function download($filename)
    {
        $path = storage_path('app/reports/' . $filename);
        if (!file_exists($path)) {
            return response()->json(['error' => 'File not found'], 404);
        }
        return response()->download($path, $filename, ['Content-Type' => 'text/csv']);
    }
}
