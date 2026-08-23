<?php

namespace App\Http\Controllers;

use App\Mail\LowStockAlertMail;
use App\Mail\NewOrderAlertMail;
use App\Mail\OrderConfirmationMail;
use App\Models\CartItem;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductRecommendation;
use App\Models\PromoCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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

        $subtotal = $validCartItems->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        $appliedPromo = null;
        $discount = 0.0;
        if ($code = session('promo_code')) {
            $promo = PromoCode::findUsable($code);
            if ($promo && $promo->isCurrentlyValid()) {
                if ($promo->scope === 'products') {
                    $promo->load('products:id');
                }
                $result = $promo->calculateDiscount($validCartItems, (float) $subtotal);
                if ($result['discount'] > 0) {
                    $appliedPromo = $promo;
                    $discount = $result['discount'];
                }
            }
            if (!$appliedPromo) {
                session()->forget('promo_code');
            }
        }

        // Use valid cart items for the view - ensure it's a collection
        $cartItemsForView = $validCartItems->values();

        return view('checkout.index', [
            'cartItems' => $cartItemsForView,
            'subtotal' => $subtotal,
            'appliedPromo' => $appliedPromo,
            'discount' => $discount,
            'total' => max(0, $subtotal - $discount),
        ]);
    }

    public function process(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|min:2|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'recipient_name' => 'nullable|string|min:2|max:255',
            'recipient_phone' => 'nullable|string|max:20',
            'delivery_address' => 'required|string|min:10|max:2000',
            'delivery_date' => 'required|date|after_or_equal:today|before_or_equal:' . now()->addDays(30)->toDateString(),
            'delivery_window' => 'required|in:morning,afternoon,anytime',
            'gift_message' => 'nullable|string|max:500',
            'delivery_instructions' => 'nullable|string|max:1000',
            'payment_method' => 'required|in:' . (config('services.dpo.company_token') ? 'dpo,whatsapp' : 'whatsapp'),
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
        $subtotal = 0;
        foreach ($cartItems as $item) {
            if ($item->product) {
                $subtotal += $item->product->price * $item->quantity;
            }
        }

        // Start database transaction to ensure data consistency
        DB::beginTransaction();

        try {
            // Re-validate the promo code against a locked row so two concurrent
            // checkouts can't both squeeze past a usage limit — never trust the
            // discount amount shown client-side.
            $appliedPromoCode = null;
            $discount = 0.0;
            if ($code = session('promo_code')) {
                $promo = PromoCode::where('code', strtoupper(trim($code)))->lockForUpdate()->first();
                if ($promo && $promo->isCurrentlyValid()
                    && ($promo->min_order_amount === null || $subtotal >= (float) $promo->min_order_amount)) {
                    if ($promo->scope === 'products') {
                        $promo->load('products:id');
                    }
                    $result = $promo->calculateDiscount($cartItems, (float) $subtotal);
                    if ($result['discount'] > 0) {
                        $appliedPromoCode = $promo;
                        $discount = $result['discount'];
                    }
                }
            }
            $total = max(0, $subtotal - $discount);

            $customerName = strip_tags($validated['customer_name']);
            $customerEmail = filter_var($validated['customer_email'], FILTER_SANITIZE_EMAIL);
            $customerPhone = strip_tags($validated['customer_phone']);
            $recipientName = strip_tags(($validated['recipient_name'] ?? null) ?: $validated['customer_name']);
            $recipientPhone = strip_tags(($validated['recipient_phone'] ?? null) ?: $validated['customer_phone']);
            $deliveryAddress = strip_tags($validated['delivery_address']);
            $giftMessage = isset($validated['gift_message']) ? strip_tags($validated['gift_message']) : null;
            $deliveryInstructions = isset($validated['delivery_instructions']) ? strip_tags($validated['delivery_instructions']) : null;

            $customer = Customer::firstOrNew(['email' => $customerEmail]);
            $customer->name = $customerName;
            $customer->phone = $customerPhone;
            $customer->address = $deliveryAddress;
            $customer->save();

            // Create order (sanitize text inputs)
            $order = Order::create([
                'customer_id' => $customer->id,
                'customer_name' => $customerName,
                'customer_email' => $customerEmail,
                'customer_phone' => $customerPhone,
                'recipient_name' => $recipientName,
                'recipient_phone' => $recipientPhone,
                'delivery_address' => $deliveryAddress,
                'delivery_date' => $validated['delivery_date'],
                'delivery_window' => $validated['delivery_window'],
                'gift_message' => $giftMessage,
                'delivery_instructions' => $deliveryInstructions,
                'total_amount' => $total,
                'promo_code' => $appliedPromoCode?->code,
                'discount_amount' => $discount,
                'payment_method' => $validated['payment_method'],
                'payment_status' => 'pending',
                'order_status' => 'pending',
            ]);

            if ($appliedPromoCode) {
                $appliedPromoCode->increment('times_used');
            }

            // Create order items and update stock
            $threshold = config('inventory.low_stock_threshold', 5);
            $lowStockCrossed = collect();

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
                $stockBefore = $product->stock;
                $product->decrement('stock', $cartItem->quantity);

                if ($stockBefore > $threshold && $product->stock <= $threshold) {
                    $lowStockCrossed->push($product);
                }

                // Update recommendations based on purchase
                $this->updateRecommendations($product->id, $cartItems->pluck('product_id')->toArray());
            }

            // Clear cart only after order is successfully created
            CartItem::where('session_id', $sessionId)->delete();
            session()->forget('promo_code');

            DB::commit();

            $this->sendOrderAlerts($order, $lowStockCrossed, $threshold);

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

    public function receipt($orderNumber)
    {
        $order = Order::with('items.product')->where('order_number', $orderNumber)->firstOrFail();
        return view('admin.orders.invoice', compact('order'));
    }

    private function sendOrderAlerts(Order $order, $lowStockCrossed, int $threshold): void
    {
        try {
            Mail::to(config('contact.email_orders'))->send(new NewOrderAlertMail($order));
        } catch (\Throwable $e) {
            Log::warning('New order alert email failed', ['order' => $order->order_number, 'error' => $e->getMessage()]);
        }

        try {
            Mail::to($order->customer_email)->send(new OrderConfirmationMail($order));
        } catch (\Throwable $e) {
            Log::warning('Order confirmation email failed', ['order' => $order->order_number, 'error' => $e->getMessage()]);
        }

        if ($lowStockCrossed->isNotEmpty()) {
            try {
                Mail::to(config('contact.email_orders'))->send(new LowStockAlertMail($lowStockCrossed, $threshold));
            } catch (\Throwable $e) {
                Log::warning('Low stock alert email failed', ['error' => $e->getMessage()]);
            }
        }
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
