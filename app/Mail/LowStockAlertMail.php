<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class LowStockAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Collection $menuItems, public int $threshold) {}

    public function envelope(): Envelope
    {
        $count = $this->menuItems->count();
        return new Envelope(
            subject: ($count === 1 ? '1 menu item is' : "{$count} menu items are") . ' low on stock',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.low-stock-alert',
            with: ['menuItems' => $this->menuItems, 'threshold' => $this->threshold],
        );
    }
}
