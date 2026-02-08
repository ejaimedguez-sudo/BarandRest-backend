<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestMail;

class SendTestMail extends Command
{
    protected $signature = 'mail:test {recipient?}';
    protected $description = 'Send a test email to the configured MAIL_REPORT_RECIPIENT or provided address';

    public function handle()
    {
        $recipient = $this->argument('recipient') ?: env('MAIL_REPORT_RECIPIENT');

        if (!$recipient) {
            $this->error('No recipient configured. Set MAIL_REPORT_RECIPIENT in .env or provide an argument.');
            return 1;
        }

        Mail::to($recipient)->send(new TestMail());

        $this->info('Test email sent to ' . $recipient);
        return 0;
    }
}
