<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
            ->limit(15)
            ->get();

        $recent = Visit::where('visited_at', '>=', $from)
            ->orderByDesc('visited_at')
            ->limit(50)
            ->get();

        $todayViews = Visit::whereDate('visited_at', $now->toDateString())->count();
        $todayUnique = (int) Visit::whereDate('visited_at', $now->toDateString())
            ->whereNotNull('session_id')
            ->selectRaw('count(distinct session_id) as c')
            ->value('c');

        return view('admin.analytics.index', [
            'period' => $period,
            'from' => $from,
            'to' => $now,
            'totalViews' => $totalViews,
            'uniqueVisitors' => $uniqueVisitors,
            'todayViews' => $todayViews,
            'todayUnique' => $todayUnique,
            'byPage' => $byPage,
            'topPaths' => $topPaths,
            'recent' => $recent,
        ]);
    }
}
