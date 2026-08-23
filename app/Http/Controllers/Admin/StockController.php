<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\LowStockAlertMail;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $threshold = config('inventory.low_stock_threshold', 5);
        $query = Product::query()->orderBy('name');

        if ($request->filled('search')) {
            $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $request->search) . '%';
            $query->where(function ($qry) use ($like) {
                $qry->where('name', 'like', $like)
                    ->orWhere('category', 'like', $like);
            });
        }

        if ($request->filled('filter')) {
            if ($request->filter === 'low') {
                $query->where('stock', '<=', $threshold);
            } elseif ($request->filter === 'out') {
                $query->where('stock', 0);
            }
        }

        $products = $query->paginate(20)->withQueryString();

        $lowStockCount = Product::where('stock', '<=', $threshold)->count();
        $outOfStockCount = Product::where('stock', 0)->count();
        $totalUnits = (int) Product::sum('stock');
        $productsCount = Product::count();
        $potentialValue = (float) Product::get()->sum(fn ($p) => $p->stock * (float) $p->price);
        $totalSales = (float) \App\Models\Order::where('order_status', '!=', 'cancelled')->sum('total_amount');

        $recentMovements = StockMovement::with(['product', 'user'])
            ->latest()
            ->take(15)
            ->get();

        return view('admin.stock.index', compact(
            'products',
            'threshold',
            'lowStockCount',
            'outOfStockCount',
            'totalUnits',
            'productsCount',
            'potentialValue',
            'totalSales',
            'recentMovements',
        ));
    }

    public function adjust(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'action' => 'required|in:set,add,subtract',
            'quantity' => 'required|integer|min:0',
            'reason' => 'nullable|string|max:500',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $before = (int) $product->stock;
        $qty = (int) $validated['quantity'];
        $action = $validated['action'];

        if ($action === 'subtract' && $qty > $before) {
            return back()->with('error', "Cannot subtract {$qty}: only {$before} in stock.");
        }

        $after = match ($action) {
            'set' => $qty,
            'add' => $before + $qty,
            'subtract' => $before - $qty,
            default => $before,
        };

        if ($after < 0) {
            return back()->with('error', 'Stock cannot go below zero.');
        }

        DB::transaction(function () use ($product, $before, $after, $action, $validated) {
            $delta = $after - $before;
            $product->update(['stock' => $after]);

            StockMovement::create([
                'product_id' => $product->id,
                'user_id' => auth()->id(),
                'type' => $action,
                'quantity_before' => $before,
                'quantity_after' => $after,
                'delta' => $delta,
                'reason' => $validated['reason'] ?? null,
            ]);
        });

        $threshold = config('inventory.low_stock_threshold', 5);
        if ($before > $threshold && $after <= $threshold) {
            try {
                Mail::to(config('contact.email_orders'))->send(new LowStockAlertMail(collect([$product]), $threshold));
            } catch (\Throwable $e) {
                Log::warning('Low stock alert email failed', ['error' => $e->getMessage()]);
            }
        }

        $msg = $action === 'set'
            ? "Stock set to {$after}."
            : ($action === 'add' ? "Added {$qty}. Stock now {$after}." : "Subtracted {$qty}. Stock now {$after}.");

        return back()->with('success', $msg);
    }
}
