<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
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

        return view('cart.index', compact('cartItems', 'total'));
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
}
