<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', '7d');
        $now = Carbon::now();
        $from = match ($period) {
            'today' => $now->copy()->startOfDay(),
            '7d' => $now->copy()->subDays(7)->startOfDay(),
            '30d' => $now->copy()->subDays(30)->startOfDay(),
            '90d' => $now->copy()->subDays(90)->startOfDay(),
            default => $now->copy()->subDays(7)->startOfDay(),
        };

        // --- Sales ---------------------------------------------------------
        $orders = Order::where('order_status', '!=', 'cancelled')
            ->whereBetween('created_at', [$from, $now])
            ->get();

        $revenue = (float) $orders->sum('total_amount');
        $orderCount = $orders->count();
        $avgOrderValue = $orderCount > 0 ? $revenue / $orderCount : 0.0;

        $ordersByStatus = Order::whereBetween('created_at', [$from, $now])
            ->select('order_status', DB::raw('count(*) as c'))
            ->groupBy('order_status')
            ->pluck('c', 'order_status');

        $ordersByPaymentMethod = Order::whereBetween('created_at', [$from, $now])
            ->select('payment_method', DB::raw('count(*) as c'))
            ->groupBy('payment_method')
            ->pluck('c', 'payment_method');

        $trendDays = (int) max(1, $from->diffInDays($now)) + 1;
        $trendDays = min($trendDays, 90);
        $trendRaw = Order::where('order_status', '!=', 'cancelled')
            ->where('created_at', '>=', $now->copy()->subDays($trendDays - 1)->startOfDay())
            ->selectRaw('DATE(created_at) as d, SUM(total_amount) as total')
            ->groupBy('d')
            ->pluck('total', 'd');
        $trendLabels = [];
        $trendData = [];
        for ($i = $trendDays - 1; $i >= 0; $i--) {
            $d = $now->copy()->subDays($i);
            $trendLabels[] = $d->format('M j');
            $trendData[] = round((float) ($trendRaw[$d->toDateString()] ?? 0), 2);
        }

        $topProducts = OrderItem::query()
            ->whereHas('order', function ($q) use ($from, $now) {
                $q->where('order_status', '!=', 'cancelled')->whereBetween('created_at', [$from, $now]);
            })
            ->with('product:id,name,image')
            ->select('product_id', DB::raw('SUM(quantity) as units'), DB::raw('SUM(subtotal) as revenue'))
            ->groupBy('product_id')
            ->orderByDesc('revenue')
            ->limit(8)
            ->get();

        // --- Site traffic (real page-view tracking, kept from before) ------
        $base = Visit::where('visited_at', '>=', $from)->where('visited_at', '<=', $now);

        $totalViews = (clone $base)->count();
        $uniqueVisitors = (int) (clone $base)->whereNotNull('session_id')->selectRaw('count(distinct session_id) as c')->value('c');

        $byPage = Visit::where('visited_at', '>=', $from)
            ->where('visited_at', '<=', $now)
            ->select('page_type', DB::raw('count(*) as hits'))
            ->groupBy('page_type')
            ->orderByDesc('hits')
            ->get();

        $topPaths = Visit::where('visited_at', '>=', $from)
            ->where('visited_at', '<=', $now)
            ->select('path', DB::raw('count(*) as hits'))
            ->groupBy('path')
            ->orderByDesc('hits')
            ->limit(10)
            ->get();

        $conversionRate = $totalViews > 0 ? ($orderCount / $totalViews) * 100 : 0.0;

        return view('admin.analytics.index', [
            'period' => $period,
            'from' => $from,
            'to' => $now,
            'revenue' => $revenue,
            'orderCount' => $orderCount,
            'avgOrderValue' => $avgOrderValue,
            'ordersByStatus' => $ordersByStatus,
            'ordersByPaymentMethod' => $ordersByPaymentMethod,
            'trendLabels' => $trendLabels,
            'trendData' => $trendData,
            'topProducts' => $topProducts,
            'totalViews' => $totalViews,
            'uniqueVisitors' => $uniqueVisitors,
            'conversionRate' => $conversionRate,
            'byPage' => $byPage,
            'topPaths' => $topPaths,
        ]);
    }
}
