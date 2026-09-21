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
            subject: 'Reset your Kaleni Catering Services admin password',
        );
    }

    public function content(): Content
    {
        $url = route('admin.reset-password', ['token' => $this->token, 'email' => $this->email]);
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
