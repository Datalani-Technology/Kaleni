<?php

namespace App\Console\Commands;

use App\Mail\PaymentReminderMail;
use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class RemindPendingDpoPayments extends Command
{
    protected $signature = 'orders:remind-pending {--hours=2 : Minimum age of the order before reminding}';

    protected $description = 'Email customers whose DPO card payment was never completed, nudging them to finish paying (once per order)';

    public function handle(): int
    {
        $hours = max(1, (int) $this->option('hours'));

        $orders = Order::where('payment_method', 'dpo')
            ->where('payment_status', 'pending')
            ->where('order_status', '!=', 'cancelled')
            ->whereNull('reminder_sent_at')
            ->where('created_at', '<=', now()->subHours($hours))
            ->get();

        if ($orders->isEmpty()) {
            $this->info('No pending DPO orders need a reminder.');
            return self::SUCCESS;
        }

        foreach ($orders as $order) {
            try {
                Mail::to($order->customer_email)->send(new PaymentReminderMail($order));
                $order->update(['reminder_sent_at' => now()]);
                $this->line("Reminded {$order->order_number}");
            } catch (\Throwable $e) {
                Log::warning('Payment reminder email failed', ['order' => $order->order_number, 'error' => $e->getMessage()]);
            }
        }

        $this->info("Sent {$orders->count()} payment reminder(s).");
        return self::SUCCESS;
    }
}
