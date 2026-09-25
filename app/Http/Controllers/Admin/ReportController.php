<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        [$from, $to] = $this->resolveRange($request);

        // Matches the Finance page's definition: revenue is money actually
        // collected (payment_status completed), not the value of everything
        // booked in the period — see FinanceController for the full reasoning.
        $bookings = Booking::where('payment_status', 'completed')
            ->where('booking_status', '!=', 'cancelled')
            ->whereBetween('created_at', [$from, $to])
            ->get();

        $revenue = (float) $bookings->sum('total_amount');
        $bookingCount = $bookings->count();

        $expenses = Expense::whereBetween('spent_at', [$from->toDateString(), $to->toDateString()])->get();
        $totalExpenses = (float) $expenses->sum('amount');
        $expensesByCategory = $expenses->groupBy('category')->map(fn ($group) => (float) $group->sum('amount'))->sortDesc();

        $netProfit = $revenue - $totalExpenses;

        return view('admin.reports.index', compact(
            'from', 'to', 'revenue', 'bookingCount', 'totalExpenses', 'expensesByCategory', 'netProfit'
        ));
    }

    public function document(Request $request)
    {
        [$from, $to] = $this->resolveRange($request);

        $bookings = Booking::with('items')
            ->where('payment_status', 'completed')
            ->where('booking_status', '!=', 'cancelled')
            ->whereBetween('created_at', [$from, $to])
            ->orderBy('created_at')
            ->get();

        $expenses = Expense::whereBetween('spent_at', [$from->toDateString(), $to->toDateString()])
            ->orderBy('spent_at')
            ->get();

        $revenue = (float) $bookings->sum('total_amount');
        $totalExpenses = (float) $expenses->sum('amount');
        $netProfit = $revenue - $totalExpenses;
        $bookingCount = $bookings->count();
        $avgBookingValue = $revenue / max(1, $bookingCount);

        return view('admin.reports.document', compact(
            'from', 'to', 'bookings', 'expenses', 'revenue', 'totalExpenses', 'netProfit', 'bookingCount', 'avgBookingValue'
        ));
    }

    private function resolveRange(Request $request): array
    {
        $from = $request->filled('from')
            ? Carbon::parse($request->from)->startOfDay()
            : Carbon::now()->startOfMonth();
        $to = $request->filled('to')
            ? Carbon::parse($request->to)->endOfDay()
            : Carbon::now()->endOfDay();

        return [$from, $to];
    }
}
