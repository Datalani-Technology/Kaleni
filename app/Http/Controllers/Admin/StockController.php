<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\LowStockAlertMail;
use App\Models\Booking;
use App\Models\MenuItem;
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
        $query = MenuItem::query()->orderBy('name');

        if ($request->filled('search')) {
            $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $request->search) . '%';
            $query->where(function ($qry) use ($like) {
                $qry->where('name', 'like', $like)
                    ->orWhere('category', 'like', $like);
            });
        }

        if ($request->filled('filter')) {
            if ($request->filter === 'low') {
                $query->where('stock', '>', 0)->where('stock', '<=', $threshold);
            } elseif ($request->filter === 'out') {
                $query->where('stock', 0);
            }
        }

        $menuItems = $query->paginate(20)->withQueryString();

        // "Low stock" and "out of stock" are deliberately mutually exclusive
        // here — otherwise every out-of-stock item would double-count into
        // the low-stock figure too, since 0 is <= any positive threshold.
        $lowStockCount = MenuItem::where('stock', '>', 0)->where('stock', '<=', $threshold)->count();
        $outOfStockCount = MenuItem::where('stock', 0)->count();
        $totalUnits = (int) MenuItem::sum('stock');
        $menuItemsCount = MenuItem::count();
        $potentialValue = (float) MenuItem::get()->sum(fn ($p) => $p->stock * (float) $p->price);
        $totalSales = (float) Booking::where('booking_status', '!=', 'cancelled')->sum('total_amount');

        $recentMovements = StockMovement::with(['menuItem', 'user'])
            ->latest()
            ->take(15)
            ->get();

        return view('admin.stock.index', compact(
            'menuItems',
            'threshold',
            'lowStockCount',
            'outOfStockCount',
            'totalUnits',
            'menuItemsCount',
            'potentialValue',
            'totalSales',
            'recentMovements',
        ));
    }

    public function adjust(Request $request)
    {
        $validated = $request->validate([
            'menu_item_id' => 'required|exists:menu_items,id',
            'action' => 'required|in:set,add,subtract',
            'quantity' => 'required|integer|min:0',
            'reason' => 'nullable|string|max:500',
        ]);

        $menuItem = MenuItem::findOrFail($validated['menu_item_id']);
        $before = (int) $menuItem->stock;
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

        DB::transaction(function () use ($menuItem, $before, $after, $action, $validated) {
            $delta = $after - $before;
            $menuItem->update(['stock' => $after]);

            StockMovement::create([
                'menu_item_id' => $menuItem->id,
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
                Mail::to(config('contact.email_orders'))->send(new LowStockAlertMail(collect([$menuItem]), $threshold));
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
