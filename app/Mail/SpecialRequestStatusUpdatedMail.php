<?php

namespace App\Mail;

use App\Models\SpecialRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SpecialRequestStatusUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public SpecialRequest $specialRequest) {}

    public function envelope(): Envelope
    {
        $subject = match ($this->specialRequest->status) {
            'quoted' => 'Your Kaleni Catering quote is ready',
            'accepted' => 'Your special request has been confirmed',
            'declined' => 'An update on your special request',
            default => 'An update on your special request',
        };

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.special-request-status-updated',
            with: ['specialRequest' => $this->specialRequest],
        );
    }
}
