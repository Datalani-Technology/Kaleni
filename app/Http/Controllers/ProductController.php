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
        
        $products = $query->latest()->paginate(12);
        return view('products.index', compact('products'));
    }

    public function show($id)
    {
        $product = Product::where('is_active', true)->findOrFail($id);
        
        // Get recommendations for this product
        $recommendations = ProductRecommendation::where('product_id', $id)
            ->with('recommendedProduct')
            ->orderBy('score', 'desc')
            ->limit(4)
            ->get()
            ->pluck('recommendedProduct')
            ->filter()
            ->where('is_active', true);

        // If no recommendations, show similar products by category
        if ($recommendations->isEmpty()) {
            $recommendations = Product::where('is_active', true)
                ->where('category', $product->category)
                ->where('id', '!=', $product->id)
                ->limit(4)
                ->get();
        }

        return view('products.show', compact('product', 'recommendations'));
    }
}
