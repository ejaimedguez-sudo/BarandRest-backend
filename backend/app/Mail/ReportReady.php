<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReportReady extends Mailable
{
    use Queueable, SerializesModels;

    public $filepath;

    public $filename;

    public function __construct(string $filepath)
    {
        $this->filepath = $filepath;
        $this->filename = basename($filepath);
    }

    public function build()
    {
        return $this->subject('Reporte diario')
            ->view('emails.report_ready')
            ->attach($this->filepath, [
                'as' => $this->filename,
                'mime' => mime_content_type($this->filepath) ?: 'application/octet-stream',
            ]);
    }
}
