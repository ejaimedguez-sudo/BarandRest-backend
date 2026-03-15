<?php

namespace App\Jobs;

use App\Exports\ReportExport;
use App\Mail\ReportReady;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class GenerateAndEmailDailyReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $from;

    public $to;

    public function __construct($from = null, $to = null)
    {
        $this->from = $from;
        $this->to = $to;
    }

    public function handle()
    {
        $request = new \Illuminate\Http\Request;
        if ($this->from) {
            $request->query->set('from', $this->from);
        }
        if ($this->to) {
            $request->query->set('to', $this->to);
        }

        $reportsController = app(\App\Http\Controllers\API\ReportsController::class);
        $reportData = $reportsController->daily($request);

        $rows = [];
        if (is_array($reportData)) {
            $rows = $reportData;
        } elseif ($reportData instanceof \Illuminate\Http\JsonResponse) {
            $rows = $reportData->getData(true);
        }

        // Generate XLSX
        $export = new ReportExport($rows);
        $xlsxFilename = 'report_'.now()->format('Ymd_His').'.xlsx';
        $xlsxPath = storage_path('app/reports/'.$xlsxFilename);
        @mkdir(dirname($xlsxPath), 0777, true);
        $export->save($xlsxPath);

        // Generate PDF
        $html = view('reports.pdf', ['rows' => $rows])->render();
        $options = new Options;
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $pdfFilename = 'report_'.now()->format('Ymd_His').'.pdf';
        $pdfPath = storage_path('app/reports/'.$pdfFilename);
        file_put_contents($pdfPath, $dompdf->output());

        // Send email to configured recipient(s)
        $recipients = explode(',', env('MAIL_REPORT_RECIPIENT', ''));
        foreach ($recipients as $to) {
            $to = trim($to);
            if (empty($to)) {
                continue;
            }
            try {
                Mail::to($to)->send(new ReportReady($xlsxPath));
            } catch (\Throwable $e) {
                // During tests, surface exception to fail fast
                if (app()->runningUnitTests()) {
                    throw $e;
                }
                // Log and continue in normal execution
                \Log::error('Error enviando reporte: '.$e->getMessage());
            }
        }
    }
}
