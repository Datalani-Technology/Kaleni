<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductRecommendation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function index()
    {
        $sessionId = session()->getId();
        $cartItems = CartItem::where('session_id', $sessionId)
            ->with('product')
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty. Please add products to your cart first.');
        }

        // Verify all products still exist and are active
        $validCartItems = $cartItems->filter(function ($item) {
            return $item->product && $item->product->is_active;
        });

        if ($validCartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'No valid products in your cart. Please add products again.');
        }

        $total = $validCartItems->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        // Use valid cart items for the view - ensure it's a collection
        $cartItemsForView = $validCartItems->values();
        
        return view('checkout.index', [
            'cartItems' => $cartItemsForView,
            'total' => $total
        ]);
    }

    public function process(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|min:2|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'delivery_address' => 'required|string|min:10|max:2000',
            'payment_method' => 'required|in:whatsapp',
        ]);

        $sessionId = session()->getId();
        
        // Get cart items - verify they belong to this session and load products
        $cartItems = CartItem::where('session_id', $sessionId)
            ->with(['product' => function($query) {
                $query->where('is_active', true);
            }])
            ->get()
            ->filter(function($item) {
                return $item->product !== null;
            });

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty. Please add products to your cart first.');
        }

        // Re-index collection
        $cartItems = $cartItems->values();

        // Check stock availability
        foreach ($cartItems as $cartItem) {
            $product = Product::find($cartItem->product_id);
            if (!$product || !$product->is_active) {
                return redirect()->route('cart.index')->with('error', "Product is no longer available.")->withInput();
            }
            if ($product->stock < $cartItem->quantity) {
                return back()->with('error', "Insufficient stock for {$product->name}. Only {$product->stock} available.")->withInput();
            }
        }

        // Calculate total - ensure products are loaded
        $total = 0;
        foreach ($cartItems as $item) {
            if ($item->product) {
                $total += $item->product->price * $item->quantity;
            }
        }

        // Start database transaction to ensure data consistency
        DB::beginTransaction();
        
        try {
            // Create order (sanitize text inputs)
            $order = Order::create([
                'customer_name' => strip_tags($validated['customer_name']),
                'customer_email' => filter_var($validated['customer_email'], FILTER_SANITIZE_EMAIL),
                'customer_phone' => strip_tags($validated['customer_phone']),
                'delivery_address' => strip_tags($validated['delivery_address']),
                'total_amount' => $total,
                'payment_method' => $validated['payment_method'],
                'payment_status' => 'pending',
                'order_status' => 'pending',
            ]);

            // Create order items and update stock
            foreach ($cartItems as $cartItem) {
                // Get fresh product data with lock to prevent race conditions
                $product = Product::lockForUpdate()->find($cartItem->product_id);
                
                if (!$product || !$product->is_active) {
                    DB::rollBack();
                    return redirect()->route('cart.index')->with('error', "Product is no longer available.")->withInput();
                }
                
                if ($product->stock < $cartItem->quantity) {
                    DB::rollBack();
                    return back()->with('error', "Insufficient stock for {$product->name}. Only {$product->stock} available.")->withInput();
                }

                // Create order item using fresh product data
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $cartItem->quantity,
                    'price' => $product->price,
                    'subtotal' => $product->price * $cartItem->quantity,
                ]);

                // Update product stock
                $product->decrement('stock', $cartItem->quantity);

                // Update recommendations based on purchase
                $this->updateRecommendations($product->id, $cartItems->pluck('product_id')->toArray());
            }

            // Clear cart only after order is successfully created
            CartItem::where('session_id', $sessionId)->delete();

            DB::commit();

            // Redirect based on payment method
            if ($request->payment_method === 'dpo') {
                return redirect()->route('payment.dpo.init', ['order' => $order->id]);
            } else {
                return redirect()->route('payment.whatsapp.init', ['order' => $order->id]);
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout error: ' . $e->getMessage(), [
                'session_id' => $sessionId,
                'cart_items_count' => $cartItems->count(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'An error occurred while processing your order. Please try again or contact support.')->withInput();
        }
    }

    public function success($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();
        return view('checkout.success', compact('order'));
    }

    private function updateRecommendations($productId, $purchasedProductIds)
    {
        foreach ($purchasedProductIds as $purchasedId) {
            if ($purchasedId == $productId) continue;

            $recommendation = ProductRecommendation::firstOrNew([
                'product_id' => $productId,
                'recommended_product_id' => $purchasedId,
            ]);

            $recommendation->score = ($recommendation->score ?? 0) + 1;
            $recommendation->save();

            // Also create reverse recommendation
            $reverseRecommendation = ProductRecommendation::firstOrNew([
                'product_id' => $purchasedId,
                'recommended_product_id' => $productId,
            ]);

            $reverseRecommendation->score = ($reverseRecommendation->score ?? 0) + 1;
            $reverseRecommendation->save();
        }
    }
}
