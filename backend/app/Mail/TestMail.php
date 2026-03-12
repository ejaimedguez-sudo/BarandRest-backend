<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function build()
    {
        return $this->subject('Ordena Facil - Test Email')
            ->view('emails.test')
            ->with(['body' => 'This is a test email from Ordena Facil.']);
    }
}
