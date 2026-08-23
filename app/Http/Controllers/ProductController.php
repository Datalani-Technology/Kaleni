<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductRecommendation;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function home()
    {
        $featuredProducts = Product::where('is_active', true)
            ->where('is_featured', true)
            ->latest()
            ->limit(8)
            ->get();
        
        return view('home', compact('featuredProducts'));
    }

    public function index(Request $request)
    {
        $query = Product::where('is_active', true);
        
        $search = $request->filled('search') ? trim($request->search) : '';
        if ($search !== '') {
            // Split into keywords (words, single chars) so "red rose" or "r" each match
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

        $categories = Product::where('is_active', true)
            ->whereNotNull('category')
            ->where('category', '<>', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $products = $query->paginate(12)->withQueryString();

        return view('products.index', compact('products', 'categories'));
    }

    public function show($id)
    {
        $product = Product::where('is_active', true)->findOrFail($id);
        
        // Get recommendations for this product
        $recommendations = ProductRecommendation::where('product_id', $id)
            ->where('recommended_product_id', '!=', $id)
            ->whereHas('recommendedProduct', fn ($query) => $query->where('is_active', true))
            ->with(['recommendedProduct' => fn ($query) => $query->where('is_active', true)])
            ->orderBy('score', 'desc')
            ->limit(8)
            ->get()
            ->pluck('recommendedProduct')
            ->filter()
            ->unique('id')
            ->take(4)
            ->values();

        if ($recommendations->count() < 4) {
            $excludeIds = $recommendations->pluck('id')->push($product->id);
            $fallback = Product::where('is_active', true)
                ->whereNotIn('id', $excludeIds)
                ->orderByRaw('CASE WHEN category = ? THEN 0 ELSE 1 END', [$product->category])
                ->orderByDesc('is_featured')
                ->limit(4 - $recommendations->count())
                ->get();

            $recommendations = $recommendations->concat($fallback)->values();
        }

        return view('products.show', compact('product', 'recommendations'));
    }
}
