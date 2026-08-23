<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\PromoCode;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $sessionId = session()->getId();
        $cartItems = CartItem::where('session_id', $sessionId)
            ->with('product')
            ->get();
        
        $total = $cartItems->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        $suggestedProducts = Product::where('is_active', true)
            ->where('stock', '>', 0)
            ->whereNotIn('id', $cartItems->pluck('product_id'))
            ->orderByDesc('is_featured')
            ->latest()
            ->limit(4)
            ->get();

        return view('cart.index', compact('cartItems', 'total', 'suggestedProducts'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);
        
        if (!$product->is_active) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Product is not available.'], 422);
            }
            return back()->with('error', 'Product is not available.');
        }

        $sessionId = session()->getId();
        
        $cartItem = CartItem::where('session_id', $sessionId)
            ->where('product_id', $request->product_id)
            ->first();

        $requestedQuantity = $request->quantity;
        if ($cartItem) {
            $requestedQuantity = $cartItem->quantity + $request->quantity;
        }

        if ($product->stock < $requestedQuantity) {
            $msg = 'Insufficient stock. Only ' . $product->stock . ' available.';
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
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
            ]);
        }

        $cartCount = CartItem::where('session_id', $sessionId)->sum('quantity');

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Product added to cart.',
                'cart_count' => (int) $cartCount,
            ]);
        }

        return back()->with('success', 'Product added to cart.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = CartItem::where('session_id', session()->getId())
            ->where('id', $id)
            ->firstOrFail();

        if ($cartItem->product->stock < $request->quantity) {
            return back()->with('error', 'Insufficient stock available.');
        }

        $cartItem->update(['quantity' => $request->quantity]);

        return back()->with('success', 'Cart updated.');
    }

    public function remove($id)
    {
        $cartItem = CartItem::where('session_id', session()->getId())
            ->where('id', $id)
            ->firstOrFail();

        $cartItem->delete();

        return back()->with('success', 'Item removed from cart.');
    }

    public function clear()
    {
        CartItem::where('session_id', session()->getId())->delete();
        return back()->with('success', 'Cart cleared.');
    }

    public function applyPromo(Request $request)
    {
        $request->validate(['code' => 'required|string|max:50']);

        $cartItems = CartItem::where('session_id', session()->getId())->with('product')->get();
        if ($cartItems->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Your cart is empty.'], 422);
        }

        $promo = PromoCode::findUsable($request->code);
        if (!$promo || !$promo->isCurrentlyValid()) {
            return response()->json(['success' => false, 'message' => 'That promo code is invalid or has expired.'], 422);
        }

        $subtotal = (float) $cartItems->sum(fn ($item) => $item->product->price * $item->quantity);

        if ($promo->min_order_amount !== null && $subtotal < (float) $promo->min_order_amount) {
            return response()->json([
                'success' => false,
                'message' => 'This code needs a minimum order of N$ ' . number_format((float) $promo->min_order_amount, 2) . '.',
            ], 422);
        }

        if ($promo->scope === 'products') {
            $promo->load('products:id');
        }

        $result = $promo->calculateDiscount($cartItems, $subtotal);

        if ($result['discount'] <= 0) {
            return response()->json(['success' => false, 'message' => "This code doesn't apply to the items in your cart."], 422);
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
