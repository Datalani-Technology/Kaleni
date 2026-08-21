<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminPasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $email,
        public string $token,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reset your /Namsa Florals admin password',
        );
    }

    public function content(): Content
    {
        $url = url('/admin/reset-password?token=' . urlencode($this->token) . '&email=' . urlencode($this->email));
        return new Content(
            view: 'emails.admin-password-reset',
            with: ['url' => $url],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
