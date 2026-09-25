<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking) {}

    public function envelope(): Envelope
    {
        $noun = $this->booking->order_type === Booking::ORDER_TYPE_QUICK_ORDER ? 'order' : 'booking';

        return new Envelope(
            subject: 'Your Kaleni Catering ' . $noun . ' ' . $this->booking->booking_number . ' is confirmed',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.booking-confirmation',
            with: [
                'booking' => $this->booking,
                'receiptUrl' => route('booking.receipt', $this->booking->booking_number),
            ],
        );
    }
}
