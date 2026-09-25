<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuickOrderRequest;
use App\Mail\BookingConfirmationMail;
use App\Mail\LowStockAlertMail;
use App\Mail\NewBookingAlertMail;
use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\CartItem;
use App\Models\Customer;
use App\Models\MenuItem;
use App\Models\MenuItemRecommendation;
use App\Models\PromoCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Quick food orders: a client orders menu items for pickup/delivery without
 * booking a full catered event (see BookingController for that flow). Both
 * flows share the same Booking/BookingItem storage, cart, promo, stock, and
 * payment machinery — only the checkout form and required fields differ,
 * distinguished by Booking::order_type.
 */
class OrderController extends Controller
{
    public function index()
    {
        $sessionId = session()->getId();
        $cartItems = CartItem::where('session_id', $sessionId)
            ->with('menuItem')
            ->get();

        $validCartItems = $cartItems->filter(function ($item) {
            return $item->menuItem && $item->menuItem->is_active;
        });

        // Mirrors BookingController::index() — a visitor can reach /order
        // directly (bookmark, back button, or a future direct link) before
        // choosing anything, so the page itself prompts for menu items
        // instead of bouncing to the cart with an error.
        if ($validCartItems->isEmpty()) {
            return view('order.index', ['cartItems' => collect()]);
        }

        $subtotal = $validCartItems->sum(function ($item) {
            return $item->menuItem->price * $item->quantity;
        });

        $appliedPromo = null;
        $discount = 0.0;
        if ($code = session('promo_code')) {
            $promo = PromoCode::findUsable($code);
            if ($promo && $promo->isCurrentlyValid()) {
                if ($promo->scope === 'products') {
                    $promo->load('menuItems:id');
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

        return view('order.index', [
            'cartItems' => $validCartItems->values(),
            'subtotal' => $subtotal,
            'appliedPromo' => $appliedPromo,
            'discount' => $discount,
            'total' => max(0, $subtotal - $discount),
        ]);
    }

    public function store(StoreQuickOrderRequest $request)
    {
        $validated = $request->validated();

        $sessionId = session()->getId();

        $cartItems = CartItem::where('session_id', $sessionId)
            ->with(['menuItem' => function ($query) {
                $query->where('is_active', true);
            }])
            ->get()
            ->filter(fn ($item) => $item->menuItem !== null)
            ->values();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your order is empty. Please add menu items first.');
        }

        foreach ($cartItems as $cartItem) {
            $menuItem = MenuItem::find($cartItem->menu_item_id);
            if (!$menuItem || !$menuItem->is_active) {
                return redirect()->route('cart.index')->with('error', 'An item in your order is no longer available.')->withInput();
            }
            if ($menuItem->stock < $cartItem->quantity) {
                return back()->with('error', "Insufficient stock for {$menuItem->name}. Only {$menuItem->stock} available.")->withInput();
            }
        }

        $subtotal = 0;
        foreach ($cartItems as $item) {
            if ($item->menuItem) {
                $subtotal += $item->menuItem->price * $item->quantity;
            }
        }

        DB::beginTransaction();

        try {
            $appliedPromoCode = null;
            $discount = 0.0;
            if ($code = session('promo_code')) {
                $promo = PromoCode::where('code', strtoupper(trim($code)))->lockForUpdate()->first();
                if ($promo && $promo->isCurrentlyValid()
                    && ($promo->min_order_amount === null || $subtotal >= (float) $promo->min_order_amount)) {
                    if ($promo->scope === 'products') {
                        $promo->load('menuItems:id');
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
            $fulfillmentMethod = $validated['fulfillment_method'];
            $deliveryAddress = $fulfillmentMethod === Booking::FULFILLMENT_DELIVERY
                ? strip_tags($validated['event_address'])
                : 'Pickup at Kaleni Catering Services — no delivery address required.';
            $orderNotes = isset($validated['event_notes']) ? strip_tags($validated['event_notes']) : null;

            $customer = Customer::firstOrNew(['email' => $customerEmail]);
            $customer->name = $customerName;
            $customer->phone = $customerPhone;
            if ($fulfillmentMethod === Booking::FULFILLMENT_DELIVERY) {
                $customer->address = $deliveryAddress;
            }
            $customer->save();

            $booking = Booking::create([
                'order_type' => Booking::ORDER_TYPE_QUICK_ORDER,
                'customer_id' => $customer->id,
                'customer_name' => $customerName,
                'customer_email' => $customerEmail,
                'customer_phone' => $customerPhone,
                'fulfillment_method' => $fulfillmentMethod,
                'event_address' => $deliveryAddress,
                'duration_mode' => 'full_day',
                'event_notes' => $orderNotes,
                'total_amount' => $total,
                'promo_code' => $appliedPromoCode?->code,
                'discount_amount' => $discount,
                'payment_method' => $validated['payment_method'],
                'payment_status' => 'pending',
                'booking_status' => 'pending',
            ]);

            if ($appliedPromoCode) {
                $appliedPromoCode->increment('times_used');
            }

            $threshold = config('inventory.low_stock_threshold', 5);
            $lowStockCrossed = collect();

            foreach ($cartItems as $cartItem) {
                $menuItem = MenuItem::lockForUpdate()->find($cartItem->menu_item_id);

                if (!$menuItem || !$menuItem->is_active) {
                    DB::rollBack();
                    return redirect()->route('cart.index')->with('error', 'An item in your order is no longer available.')->withInput();
                }

                if ($menuItem->stock < $cartItem->quantity) {
                    DB::rollBack();
                    return back()->with('error', "Insufficient stock for {$menuItem->name}. Only {$menuItem->stock} available.")->withInput();
                }

                BookingItem::create([
                    'booking_id' => $booking->id,
                    'menu_item_id' => $menuItem->id,
                    'quantity' => $cartItem->quantity,
                    'price' => $menuItem->price,
                    'subtotal' => $menuItem->price * $cartItem->quantity,
                ]);

                $stockBefore = $menuItem->stock;
                $menuItem->decrement('stock', $cartItem->quantity);

                if ($stockBefore > $threshold && $menuItem->stock <= $threshold) {
                    $lowStockCrossed->push($menuItem);
                }

                $this->updateRecommendations($menuItem->id, $cartItems->pluck('menu_item_id')->toArray());
            }

            CartItem::where('session_id', $sessionId)->delete();
            session()->forget('promo_code');

            DB::commit();

            $this->sendOrderAlerts($booking, $lowStockCrossed, $threshold);

            $isWhatsapp = $request->payment_method !== 'dpo';
            $nextUrl = $isWhatsapp
                ? route('payment.whatsapp.init', ['booking' => $booking->id])
                : route('payment.dpo.init', ['booking' => $booking->id]);

            // The checkout page submits this via fetch specifically so that
            // the hand-off to an external payment/WhatsApp URL happens as a
            // separate, plain navigation afterwards — the site's CSP allows
            // this form to submit only to itself (form-action 'self'), and
            // that restriction is enforced against the whole redirect chain,
            // not just the first hop.
            if ($request->wantsJson()) {
                // WhatsApp is a hand-off, not a destination — the customer
                // should land back on the site's own confirmation page (with
                // its pop-up success message) while WhatsApp opens
                // separately, rather than being navigated away with nothing
                // to show for it. DPO isn't finished yet at this point (the
                // gateway itself still has to run), so it keeps navigating
                // the current tab straight there.
                if ($isWhatsapp) {
                    session()->flash('success', 'Order ' . $booking->booking_number . ' confirmed! Continue in the new WhatsApp tab to finalize the details.');

                    return response()->json([
                        'redirect' => route('booking.success', $booking->booking_number),
                        'whatsapp_redirect' => $nextUrl,
                    ]);
                }

                return response()->json(['redirect' => $nextUrl]);
            }

            return redirect($nextUrl);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Quick order error: ' . $e->getMessage(), [
                'session_id' => $sessionId,
                'cart_items_count' => $cartItems->count(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->with('error', 'An error occurred while processing your order. Please try again or contact us.')->withInput();
        }
    }

    private function sendOrderAlerts(Booking $booking, $lowStockCrossed, int $threshold): void
    {
        try {
            Mail::to(config('contact.email_orders'))->send(new NewBookingAlertMail($booking));
        } catch (\Throwable $e) {
            Log::warning('New order alert email failed', ['booking' => $booking->booking_number, 'error' => $e->getMessage()]);
        }

        try {
            Mail::to($booking->customer_email)->send(new BookingConfirmationMail($booking));
        } catch (\Throwable $e) {
            Log::warning('Order confirmation email failed', ['booking' => $booking->booking_number, 'error' => $e->getMessage()]);
        }

        if ($lowStockCrossed->isNotEmpty()) {
            try {
                Mail::to(config('contact.email_orders'))->send(new LowStockAlertMail($lowStockCrossed, $threshold));
            } catch (\Throwable $e) {
                Log::warning('Low stock alert email failed', ['error' => $e->getMessage()]);
            }
        }
    }

    private function updateRecommendations($menuItemId, $orderedMenuItemIds)
    {
        foreach ($orderedMenuItemIds as $orderedId) {
            if ($orderedId == $menuItemId) continue;

            $recommendation = MenuItemRecommendation::firstOrNew([
                'menu_item_id' => $menuItemId,
                'recommended_menu_item_id' => $orderedId,
            ]);

            $recommendation->score = ($recommendation->score ?? 0) + 1;
            $recommendation->save();

            $reverseRecommendation = MenuItemRecommendation::firstOrNew([
                'menu_item_id' => $orderedId,
                'recommended_menu_item_id' => $menuItemId,
            ]);

            $reverseRecommendation->score = ($reverseRecommendation->score ?? 0) + 1;
            $reverseRecommendation->save();
        }
    }
}
