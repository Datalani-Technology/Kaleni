<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        [$from, $to] = $this->resolveRange($request);

        $orders = Order::where('order_status', '!=', 'cancelled')
            ->whereBetween('created_at', [$from, $to])
            ->get();

        $revenue = (float) $orders->sum('total_amount');
        $orderCount = $orders->count();

        $expenses = Expense::whereBetween('spent_at', [$from->toDateString(), $to->toDateString()])->get();
        $totalExpenses = (float) $expenses->sum('amount');
        $expensesByCategory = $expenses->groupBy('category')->map(fn ($group) => (float) $group->sum('amount'))->sortDesc();

        $netProfit = $revenue - $totalExpenses;

        return view('admin.reports.index', compact(
            'from', 'to', 'revenue', 'orderCount', 'totalExpenses', 'expensesByCategory', 'netProfit'
        ));
    }

    public function document(Request $request)
    {
        [$from, $to] = $this->resolveRange($request);

        $orders = Order::with('items')
            ->where('order_status', '!=', 'cancelled')
            ->whereBetween('created_at', [$from, $to])
            ->orderBy('created_at')
            ->get();

        $expenses = Expense::whereBetween('spent_at', [$from->toDateString(), $to->toDateString()])
            ->orderBy('spent_at')
            ->get();

        $revenue = (float) $orders->sum('total_amount');
        $totalExpenses = (float) $expenses->sum('amount');
        $netProfit = $revenue - $totalExpenses;
        $orderCount = $orders->count();
        $avgOrderValue = $revenue / max(1, $orderCount);

        return view('admin.reports.document', compact(
            'from', 'to', 'orders', 'expenses', 'revenue', 'totalExpenses', 'netProfit', 'orderCount', 'avgOrderValue'
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
