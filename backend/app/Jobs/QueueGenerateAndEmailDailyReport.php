<?php

namespace App\Jobs;

use App\Mail\ReportReady;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class QueueGenerateAndEmailDailyReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $date;

    public $groupBy;

    public $emailTo;

    public function __construct(string $date, ?string $groupBy = null, ?string $emailTo = null)
    {
        $this->date = $date;
        $this->groupBy = $groupBy;
        $this->emailTo = $emailTo;
    }

    public function handle()
    {
        // Generate CSV using existing Job
        $job = new GenerateDailyReportCsv($this->date, $this->groupBy);
        $filename = $job->handle();
        $path = storage_path('app/reports/'.$filename);

        if ($this->emailTo && file_exists($path)) {
            Mail::to($this->emailTo)->send(new ReportReady($filename, $path));
        }
    }
}
