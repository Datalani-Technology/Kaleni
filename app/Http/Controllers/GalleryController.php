<?php

namespace App\Http\Controllers;

use App\Models\GalleryItem;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function index(Request $request)
    {
        $activeOccasion = $request->query('occasion');
        if (!in_array($activeOccasion, GalleryItem::OCCASIONS, true)) {
            $activeOccasion = null;
        }

        // Only offer chips for occasions that actually have at least one photo,
        // so we never show an empty category.
        $availableOccasions = GalleryItem::whereNotNull('occasion')
            ->distinct()
            ->pluck('occasion')
            ->filter(fn ($occasion) => in_array($occasion, GalleryItem::OCCASIONS, true))
            ->sortBy(fn ($occasion) => array_search($occasion, GalleryItem::OCCASIONS))
            ->values();

        $query = GalleryItem::orderBy('sort_order')->orderByDesc('created_at');
        if ($activeOccasion) {
            $query->where('occasion', $activeOccasion);
        }
        $items = $query->paginate(24)->withQueryString();

        return view('gallery', compact('items', 'availableOccasions', 'activeOccasion'));
    }
}
