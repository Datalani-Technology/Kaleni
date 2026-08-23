<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewOrderAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New order ' . $this->order->order_number . ' - N$ ' . number_format((float) $this->order->total_amount, 2),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new-order-alert',
            with: ['order' => $this->order],
        );
    }
}
