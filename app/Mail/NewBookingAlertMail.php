<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewBookingAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking) {}

    public function envelope(): Envelope
    {
        $noun = $this->booking->order_type === Booking::ORDER_TYPE_QUICK_ORDER ? 'order' : 'booking';

        return new Envelope(
            subject: 'New ' . $noun . ' ' . $this->booking->booking_number . ' - N$ ' . number_format((float) $this->booking->total_amount, 2),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new-booking-alert',
            with: ['booking' => $this->booking],
        );
    }
}
