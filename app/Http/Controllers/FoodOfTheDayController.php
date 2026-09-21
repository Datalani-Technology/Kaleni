<?php

namespace App\Http\Controllers;

use App\Models\FoodOfTheDay;
use Illuminate\Support\Carbon;

class FoodOfTheDayController extends Controller
{
    /**
     * Today's special plus the rest of the scheduled week, so clients can
     * see what's coming up and plan a booking around it.
     */
    public function index()
    {
        $today = Carbon::today();
        $weekAhead = $today->copy()->addDays(6);

        $scheduled = FoodOfTheDay::with('menuItem')
            ->where('is_active', true)
            ->whereDate('serve_date', '>=', $today)
            ->whereDate('serve_date', '<=', $weekAhead)
            ->orderBy('serve_date')
            ->get()
            ->keyBy(fn ($entry) => $entry->serve_date->toDateString());

        // Always exactly 7 days — including the ones nothing is scheduled for
        // yet — so the page reads as a complete weekly line-up rather than a
        // sparse list that shrinks to almost nothing on a quiet week.
        $week = collect(range(0, 6))->map(function ($offset) use ($today, $scheduled) {
            $date = $today->copy()->addDays($offset);

            return (object) [
                'date' => $date,
                'entry' => $scheduled->get($date->toDateString()),
            ];
        });

        $todayEntry = $week->first()->entry;

        return view('food-of-the-day.index', compact('week', 'todayEntry'));
    }
}
