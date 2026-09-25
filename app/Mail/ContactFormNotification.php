<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactFormNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $name,
        public string $email,
        public string $contactSubject,
        public string $contactMessage,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[Contact] ' . $this->contactSubject,
            replyTo: [$this->email],
        );
    }

    /**
     * View data keys avoid "message" (and would avoid "subject" too if it
     * caused the same problem) — Laravel injects its own Illuminate\Mail\Message
     * instance into every mail view under the name "message" for things like
     * $message->embed(), silently shadowing a same-named Mailable property/view
     * variable and breaking any {{ $message }} usage in the template.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-notification',
            with: [
                'name' => $this->name,
                'email' => $this->email,
                'subject' => $this->contactSubject,
                'messageBody' => $this->contactMessage,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
