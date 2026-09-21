<?php

namespace App\Mail;

use App\Models\SpecialRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewSpecialRequestAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public SpecialRequest $specialRequest) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New special item request from ' . $this->specialRequest->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new-special-request-alert',
            with: ['specialRequest' => $this->specialRequest],
        );
    }
}
