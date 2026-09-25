<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Expense;
use Carbon\Carbon;

class FinanceController extends Controller
{
    public function index()
    {
        $now = Carbon::now();

        // "Revenue" means money actually collected, not the value of
        // everything booked — a booking only counts once its payment_status
        // is manually marked completed by an admin (there's no live payment
        // gateway confirming WhatsApp payments). Counting pending-payment
        // bookings as revenue would overstate real income, so those are
        // surfaced separately below as "awaiting payment" instead of being
        // folded in silently.
        $collected = Booking::where('payment_status', 'completed')
            ->where('booking_status', '!=', 'cancelled');

        $baseSales = fn ($from, $to) => (float) (clone $collected)
            ->whereBetween('created_at', [$from, $to])->sum('total_amount');
        $baseExpenses = fn ($from, $to) => (float) Expense::whereBetween('spent_at', [$from->toDateString(), $to->toDateString()])->sum('amount');

        $salesMonth = $baseSales($now->copy()->startOfMonth(), $now->copy()->endOfMonth());
        $salesAllTime = (float) (clone $collected)->sum('total_amount');

        $expensesMonth = $baseExpenses($now->copy()->startOfMonth(), $now->copy()->endOfMonth());
        $expensesAllTime = (float) Expense::sum('amount');

        $netMonth = $salesMonth - $expensesMonth;
        $netAllTime = $salesAllTime - $expensesAllTime;

        $bookingCount = (clone $collected)->count();
        $avgBookingValue = $salesAllTime / max(1, $bookingCount);

        // Bookings/orders that are confirmed (not cancelled) but not yet
        // paid — real, active business that Revenue above deliberately
        // excludes until the money is actually in hand.
        $awaitingBookings = Booking::where('payment_status', 'pending')
            ->where('booking_status', '!=', 'cancelled')
            ->where('order_type', Booking::ORDER_TYPE_CATERING_BOOKING);
        $awaitingOrders = Booking::where('payment_status', 'pending')
            ->where('booking_status', '!=', 'cancelled')
            ->where('order_type', Booking::ORDER_TYPE_QUICK_ORDER);
        $awaitingBookingCount = (clone $awaitingBookings)->count();
        $awaitingBookingTotal = (float) (clone $awaitingBookings)->sum('total_amount');
        $awaitingOrderCount = (clone $awaitingOrders)->count();
        $awaitingOrderTotal = (float) (clone $awaitingOrders)->sum('total_amount');

        // Who, specifically, is owed a follow-up — a total by itself just
        // tells you a number exists, not who to chase for payment.
        $awaitingList = Booking::where('payment_status', 'pending')
            ->where('booking_status', '!=', 'cancelled')
            ->latest()
            ->take(8)
            ->get(['id', 'booking_number', 'customer_name', 'order_type', 'total_amount', 'created_at']);
        $awaitingTotalCount = $awaitingBookingCount + $awaitingOrderCount;

        // Payment attempts that failed need a human to follow up (retry,
        // re-invoice, or write off) — flagged here rather than mixed into
        // either revenue or the "awaiting payment" total.
        $failedPaymentCount = Booking::where('payment_status', 'failed')
            ->where('booking_status', '!=', 'cancelled')
            ->count();

        $trendRaw = (clone $collected)
            ->where('created_at', '>=', $now->copy()->subDays(29)->startOfDay())
            ->selectRaw('DATE(created_at) as d, SUM(total_amount) as total')
            ->groupBy('d')
            ->pluck('total', 'd');
        $trendLabels = [];
        $trendData = [];
        for ($i = 29; $i >= 0; $i--) {
            $d = $now->copy()->subDays($i);
            $trendLabels[] = $d->format('M j');
            $trendData[] = round((float) ($trendRaw[$d->toDateString()] ?? 0), 2);
        }

        return view('admin.finance.index', compact(
            'salesMonth', 'salesAllTime',
            'expensesMonth', 'expensesAllTime',
            'netMonth', 'netAllTime', 'bookingCount', 'avgBookingValue',
            'awaitingBookingCount', 'awaitingBookingTotal',
            'awaitingOrderCount', 'awaitingOrderTotal',
            'awaitingList', 'awaitingTotalCount',
            'failedPaymentCount',
            'trendLabels', 'trendData'
        ));
    }
}
