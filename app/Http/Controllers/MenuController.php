<?php

namespace App\Http\Controllers;

use App\Models\FoodOfTheDay;
use App\Models\MenuItem;
use App\Models\MenuItemRecommendation;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function home()
    {
        $featuredMenuItems = MenuItem::where('is_active', true)
            ->where('is_featured', true)
            ->latest()
            ->limit(8)
            ->get();

        $foodOfTheDay = FoodOfTheDay::forToday();

        return view('home', compact('featuredMenuItems', 'foodOfTheDay'));
    }

    /**
     * Icon shown on each category tab in the menu catalog nav. Falls back to
     * a generic dish icon for any category not listed here.
     */
    public const CATEGORY_ICONS = [
        'Lunch & Dinner Packs' => 'bi-bag',
        'Individual Meals' => 'bi-egg-fried',
        'Sharing Platters' => 'bi-people',
        'Traditional' => 'bi-cup',
    ];

    public function index(Request $request)
    {
        $categories = MenuItem::where('is_active', true)
            ->whereNotNull('category')
            ->where('category', '<>', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $query = MenuItem::where('is_active', true);

        $search = $request->filled('search') ? trim($request->search) : '';
        if ($search !== '') {
            // Split into keywords (words, single chars) so "chicken wrap" or "w" each match
            $keywords = preg_split('/\s+/', $search, -1, PREG_SPLIT_NO_EMPTY);
            if (!empty($keywords)) {
                $query->where(function ($q) use ($keywords) {
                    foreach ($keywords as $keyword) {
                        $escaped = str_replace(['%', '_'], ['\%', '\_'], $keyword);
                        $like = '%' . $escaped . '%';
                        $q->orWhere(function ($q2) use ($like) {
                            $q2->where('name', 'like', $like)
                                ->orWhere('description', 'like', $like)
                                ->orWhere('category', 'like', $like);
                        });
                    }
                });
            }
        }

        $category = $request->filled('category') ? trim((string) $request->category) : '';
        if ($category !== '') {
            $query->where('category', $category);
        }

        if ($request->filled('min_price') && is_numeric($request->min_price)) {
            $query->where('price', '>=', max(0, (float) $request->min_price));
        }

        if ($request->filled('max_price') && is_numeric($request->max_price)) {
            $query->where('price', '<=', max(0, (float) $request->max_price));
        }

        if ($request->boolean('in_stock')) {
            $query->where('stock', '>', 0);
        }

        match ($request->input('sort', 'featured')) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'name' => $query->orderBy('name'),
            'newest' => $query->latest(),
            default => $query->orderByDesc('is_featured')->latest(),
        };

        $menuItems = $query->paginate(8)->withQueryString();

        return view('menu.index', compact('menuItems', 'categories'));
    }

    public function show($id)
    {
        $menuItem = MenuItem::where('is_active', true)->findOrFail($id);

        // Get recommendations for this item ("often ordered together")
        $recommendations = MenuItemRecommendation::where('menu_item_id', $id)
            ->where('recommended_menu_item_id', '!=', $id)
            ->whereHas('recommendedMenuItem', fn ($query) => $query->where('is_active', true))
            ->with(['recommendedMenuItem' => fn ($query) => $query->where('is_active', true)])
            ->orderBy('score', 'desc')
            ->limit(8)
            ->get()
            ->pluck('recommendedMenuItem')
            ->filter()
            ->unique('id')
            ->take(4)
            ->values();

        if ($recommendations->count() < 4) {
            $excludeIds = $recommendations->pluck('id')->push($menuItem->id);
            $fallback = MenuItem::where('is_active', true)
                ->whereNotIn('id', $excludeIds)
                ->orderByRaw('CASE WHEN category = ? THEN 0 ELSE 1 END', [$menuItem->category])
                ->orderByDesc('is_featured')
                ->limit(4 - $recommendations->count())
                ->get();

            $recommendations = $recommendations->concat($fallback)->values();
        }

        return view('menu.show', compact('menuItem', 'recommendations'));
    }
}
