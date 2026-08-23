<?php

namespace Tests\Feature;

use App\Models\CartItem;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // The cart/checkout flow is keyed by the session cookie, so these
        // tests need a driver that actually issues one across requests
        // (the suite-wide 'array' driver doesn't queue a session cookie).
        config(['session.driver' => 'file']);
    }

    public function test_guest_can_complete_checkout_and_stock_and_customer_update(): void
    {
        Mail::fake();

        $product = Product::create([
            'name' => 'Test Roses',
            'description' => 'A dozen red roses.',
            'price' => 250.00,
            'stock' => 10,
            'category' => 'Roses',
            'is_active' => true,
            'is_featured' => false,
        ]);

        $addResponse = $this->post('/cart/add', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
        $addResponse->assertSessionHasNoErrors();
        $this->carrySessionCookieForward($addResponse);

        $response = $this->post('/checkout', [
            'customer_name' => 'Jane Doe',
            'customer_email' => 'jane@example.com',
            'customer_phone' => '+264811234567',
            'delivery_address' => '123 Independence Ave, Windhoek',
            'payment_method' => 'whatsapp',
        ]);

        $response->assertRedirect();

        $order = Order::where('customer_email', 'jane@example.com')->first();
        $this->assertNotNull($order, 'Order was not created.');
        $this->assertEquals(500.00, (float) $order->total_amount);
        $this->assertEquals('pending', $order->order_status);

        $product->refresh();
        $this->assertEquals(8, $product->stock, 'Stock was not decremented correctly.');

        $customer = Customer::where('email', 'jane@example.com')->first();
        $this->assertNotNull($customer, 'Customer record was not created.');
        $this->assertEquals($customer->id, $order->customer_id);

        $this->assertEquals(0, CartItem::where('product_id', $product->id)->count(), 'Cart was not cleared.');
    }

    public function test_checkout_rejects_order_exceeding_available_stock(): void
    {
        Mail::fake();

        $product = Product::create([
            'name' => 'Limited Tulips',
            'price' => 100.00,
            'stock' => 5,
            'is_active' => true,
        ]);

        $addResponse = $this->post('/cart/add', [
            'product_id' => $product->id,
            'quantity' => 5,
        ]);
        $addResponse->assertSessionHasNoErrors();
        $this->carrySessionCookieForward($addResponse);

        // Simulate stock dropping (e.g. another customer bought it) between
        // add-to-cart and checkout — checkout must still refuse to oversell.
        $product->update(['stock' => 1]);

        $response = $this->post('/checkout', [
            'customer_name' => 'John Smith',
            'customer_email' => 'john@example.com',
            'customer_phone' => '+264811234568',
            'delivery_address' => '456 Independence Ave, Windhoek',
            'payment_method' => 'whatsapp',
        ]);

        $response->assertSessionHas('error', function ($message) {
            return str_contains($message, 'Insufficient stock');
        });
        $this->assertEquals(0, Order::where('customer_email', 'john@example.com')->count());

        $product->refresh();
        $this->assertEquals(1, $product->stock, 'Stock should be unchanged on a rejected order.');
    }

    /**
     * The test client doesn't automatically replay Set-Cookie headers on the
     * next request, so the session (and the cart it holds) would otherwise
     * look empty on each subsequent call. Forward the real session cookie
     * from the last response, exactly like a browser would.
     */
    private function carrySessionCookieForward($response): void
    {
        $name = config('session.cookie');
        $cookie = collect($response->headers->getCookies())
            ->first(fn ($c) => $c->getName() === $name);
        $this->assertNotNull($cookie, "Session cookie [{$name}] was not set on the response.");

        // The response cookie is already encrypted (EncryptCookies ran on the way
        // out); withCookie() encrypts again on the way in, so decrypt + strip the
        // CookieValuePrefix first to hand it the plain session ID it expects.
        $decrypted = decrypt($cookie->getValue(), false);
        $sessionId = \Illuminate\Cookie\CookieValuePrefix::remove($decrypted);

        $this->withCookie($name, $sessionId);
    }
}
