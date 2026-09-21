<?php

namespace App\Mail;

use App\Models\SpecialRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SpecialRequestReceivedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public SpecialRequest $specialRequest) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'We received your special request, Kaleni Catering Services',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.special-request-received',
            with: ['specialRequest' => $this->specialRequest],
        );
    }
}
