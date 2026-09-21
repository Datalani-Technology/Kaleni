<?php

namespace App\Console\Commands;

use App\Mail\PaymentReminderMail;
use App\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class RemindPendingDpoPayments extends Command
{
    protected $signature = 'bookings:remind-pending {--hours=2 : Minimum age of the booking before reminding}';

    protected $description = 'Email clients whose DPO card payment was never completed, nudging them to finish paying (once per booking)';

    public function handle(): int
    {
        $hours = max(1, (int) $this->option('hours'));

        $bookings = Booking::where('payment_method', 'dpo')
            ->where('payment_status', 'pending')
            ->where('booking_status', '!=', 'cancelled')
            ->whereNull('reminder_sent_at')
            ->where('created_at', '<=', now()->subHours($hours))
            ->get();

        if ($bookings->isEmpty()) {
            $this->info('No pending DPO bookings need a reminder.');
            return self::SUCCESS;
        }

        foreach ($bookings as $booking) {
            try {
                Mail::to($booking->customer_email)->send(new PaymentReminderMail($booking));
                $booking->update(['reminder_sent_at' => now()]);
                $this->line("Reminded {$booking->booking_number}");
            } catch (\Throwable $e) {
                Log::warning('Payment reminder email failed', ['booking' => $booking->booking_number, 'error' => $e->getMessage()]);
            }
        }

        $this->info("Sent {$bookings->count()} payment reminder(s).");
        return self::SUCCESS;
    }
}
