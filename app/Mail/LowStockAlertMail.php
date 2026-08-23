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

    public function __construct(public Collection $products, public int $threshold) {}

    public function envelope(): Envelope
    {
        $count = $this->products->count();
        return new Envelope(
            subject: ($count === 1 ? '1 product is' : "{$count} products are") . ' low on stock',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.low-stock-alert',
            with: ['products' => $this->products, 'threshold' => $this->threshold],
        );
    }
}
