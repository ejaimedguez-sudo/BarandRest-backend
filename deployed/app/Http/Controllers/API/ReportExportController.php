<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Exports\ReportExport;
use Dompdf\Dompdf;
use Dompdf\Options;

class ReportExportController extends Controller
{
    public function exportExcel(Request $request)
    {
        $from = $request->query('from');
        $to = $request->query('to');
        $groupBy = $request->query('group_by', 'daily');

        // Build report data using existing ReportsController logic via HTTP internal call
        $reportData = app(\App\Http\Controllers\API\ReportsController::class)->daily($request);

        // If ReportsController returned a response, extract data array if present
        $rows = [];
        if (is_array($reportData)) {
            $rows = $reportData;
        } elseif ($reportData instanceof \Illuminate\Http\JsonResponse) {
            $rows = $reportData->getData(true);
        }

        $export = new ReportExport($rows);
        $filename = 'report_' . now()->format('Ymd_His') . '.xlsx';
        $path = storage_path('app/reports/' . $filename);

        try {
            $export->save($path);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'No se pudo generar Excel: ' . $e->getMessage()], 500);
        }

        return response()->download($path, $filename);
    }

    public function exportPdf(Request $request)
    {
        $from = $request->query('from');
        $to = $request->query('to');
        $groupBy = $request->query('group_by', 'daily');

        $reportsController = app(\App\Http\Controllers\API\ReportsController::class);
        $reportData = $reportsController->daily($request);

        $rows = [];
        if (is_array($reportData)) {
            $rows = $reportData;
        } elseif ($reportData instanceof \Illuminate\Http\JsonResponse) {
            $rows = $reportData->getData(true);
        }

        // compute totals if rows contain numeric keys
        $rowsArray = [];
        if (is_array($rows)) $rowsArray = $rows;
        elseif ($rows instanceof \Illuminate\Support\Collection) $rowsArray = $rows->map(fn($r)=> (array)$r)->toArray();

        $totals = [];
        if (!empty($rowsArray) && is_array($rowsArray[0])) {
            foreach ($rowsArray as $r) {
                foreach ($r as $k => $v) {
                    if (is_numeric($v)) {
                        $totals[$k] = ($totals[$k] ?? 0) + $v;
                    }
                }
            }
        }

        $html = view('reports.pdf', ['rows' => $rowsArray, 'totals' => $totals])->render();

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $output = $dompdf->output();
        $filename = 'report_' . now()->format('Ymd_His') . '.pdf';
        $path = storage_path('app/reports/' . $filename);
        file_put_contents($path, $output);

        return response()->download($path, $filename);
    }
}
