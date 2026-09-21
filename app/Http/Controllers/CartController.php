<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\MenuItem;
use App\Models\PromoCode;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $sessionId = session()->getId();
        $cartItems = CartItem::where('session_id', $sessionId)
            ->with('menuItem')
            ->get();

        $total = $cartItems->sum(function ($item) {
            return $item->menuItem->price * $item->quantity;
        });

        $suggestedMenuItems = MenuItem::where('is_active', true)
            ->where('stock', '>', 0)
            ->whereNotIn('id', $cartItems->pluck('menu_item_id'))
            ->orderByDesc('is_featured')
            ->latest()
            ->limit(4)
            ->get();

        return view('cart.index', compact('cartItems', 'total', 'suggestedMenuItems'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'menu_item_id' => 'required|exists:menu_items,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $menuItem = MenuItem::findOrFail($request->menu_item_id);

        if (!$menuItem->is_active) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'This item is not available.'], 422);
            }
            return back()->with('error', 'This item is not available.');
        }

        $sessionId = session()->getId();

        $cartItem = CartItem::where('session_id', $sessionId)
            ->where('menu_item_id', $request->menu_item_id)
            ->first();

        $requestedQuantity = $request->quantity;
        if ($cartItem) {
            $requestedQuantity = $cartItem->quantity + $request->quantity;
        }

        if ($menuItem->stock < $requestedQuantity) {
            $msg = 'Insufficient stock. Only ' . $menuItem->stock . ' available.';
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return back()->with('error', $msg);
        }

        if ($cartItem) {
            $cartItem->update(['quantity' => $requestedQuantity]);
        } else {
            CartItem::create([
                'session_id' => $sessionId,
                'menu_item_id' => $request->menu_item_id,
                'quantity' => $request->quantity,
            ]);
        }

        $cartCount = CartItem::where('session_id', $sessionId)->sum('quantity');

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Added to your order.',
                'cart_count' => (int) $cartCount,
            ]);
        }

        return back()->with('success', 'Added to your order.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = CartItem::where('session_id', session()->getId())
            ->where('id', $id)
            ->firstOrFail();

        if ($cartItem->menuItem->stock < $request->quantity) {
            return back()->with('error', 'Insufficient stock available.');
        }

        $cartItem->update(['quantity' => $request->quantity]);

        return back()->with('success', 'Order updated.');
    }

    public function remove($id)
    {
        $cartItem = CartItem::where('session_id', session()->getId())
            ->where('id', $id)
            ->firstOrFail();

        $cartItem->delete();

        return back()->with('success', 'Item removed from your order.');
    }

    public function clear()
    {
        CartItem::where('session_id', session()->getId())->delete();
        return back()->with('success', 'Order cleared.');
    }

    public function applyPromo(Request $request)
    {
        $request->validate(['code' => 'required|string|max:50']);

        $cartItems = CartItem::where('session_id', session()->getId())->with('menuItem')->get();
        if ($cartItems->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Your order is empty.'], 422);
        }

        $promo = PromoCode::findUsable($request->code);
        if (!$promo || !$promo->isCurrentlyValid()) {
            return response()->json(['success' => false, 'message' => 'That promo code is invalid or has expired.'], 422);
        }

        $subtotal = (float) $cartItems->sum(fn ($item) => $item->menuItem->price * $item->quantity);

        if ($promo->min_order_amount !== null && $subtotal < (float) $promo->min_order_amount) {
            return response()->json([
                'success' => false,
                'message' => 'This code needs a minimum order of N$ ' . number_format((float) $promo->min_order_amount, 2) . '.',
            ], 422);
        }

        if ($promo->scope === 'products') {
            $promo->load('menuItems:id');
        }

        $result = $promo->calculateDiscount($cartItems, $subtotal);

        if ($result['discount'] <= 0) {
            return response()->json(['success' => false, 'message' => "This code doesn't apply to the items in your order."], 422);
        }

        session(['promo_code' => $promo->code]);

        return response()->json([
            'success' => true,
            'message' => 'Promo code applied.',
            'code' => $promo->code,
            'discount' => $result['discount'],
            'subtotal' => round($subtotal, 2),
            'total' => round($subtotal - $result['discount'], 2),
        ]);
    }

    public function removePromo()
    {
        session()->forget('promo_code');
        return response()->json(['success' => true]);
    }
}
