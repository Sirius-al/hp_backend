<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SmoEmailForQueuing extends Mailable
{
    use Queueable, SerializesModels;
    public $service_request;

    public function __construct($service_request)
    {
        $this->service_request = $service_request;
    }

    public function envelope()
    {
        return new Envelope(
            subject: $this->service_request['subject'],
        );
    }

    public function content()
    {
        return new Content(
            view: 'mails.smo_mail',
            with: [
                'service_request_id' => $this->service_request['ID'],
            ],
        );
    }
    public function attachments()
    {
        return [];
    }
}
