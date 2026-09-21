<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\CartItem;
use App\Models\Customer;
use App\Models\MenuItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // The cart/booking flow is keyed by the session cookie, so these
        // tests need a driver that actually issues one across requests
        // (the suite-wide 'array' driver doesn't queue a session cookie).
        config(['session.driver' => 'file']);
    }

    public function test_guest_can_complete_booking_and_stock_and_customer_update(): void
    {
        Mail::fake();

        $menuItem = MenuItem::create([
            'name' => 'Test Braai Platter',
            'description' => 'A grilled meat platter.',
            'price' => 250.00,
            'stock' => 10,
            'category' => 'Sharing Platters',
            'is_active' => true,
            'is_featured' => false,
        ]);

        $addResponse = $this->post('/cart/add', [
            'menu_item_id' => $menuItem->id,
            'quantity' => 2,
        ]);
        $addResponse->assertSessionHasNoErrors();
        $this->carrySessionCookieForward($addResponse);

        $response = $this->post('/booking', [
            'customer_name' => 'Jane Doe',
            'customer_email' => 'jane@example.com',
            'customer_phone' => '+264811234567',
            'event_address' => '123 Independence Ave, Windhoek',
            'event_date' => now()->addDays(3)->toDateString(),
            'serving_period' => 'lunch',
            'duration_mode' => 'hours',
            'start_time' => '12:00',
            'duration_hours' => 4,
            'guest_count' => 20,
            'event_type' => 'Birthday',
            'payment_method' => 'whatsapp',
        ]);

        $response->assertRedirect();

        $booking = Booking::where('customer_email', 'jane@example.com')->first();
        $this->assertNotNull($booking, 'Booking was not created.');
        $this->assertEquals(500.00, (float) $booking->total_amount);
        $this->assertEquals('pending', $booking->booking_status);
        $this->assertEquals(20, $booking->guest_count);
        $this->assertEquals('lunch', $booking->serving_period);
        $this->assertEquals('Birthday', $booking->event_type);
        $this->assertEquals('hours', $booking->duration_mode);
        $this->assertEquals(4, $booking->duration_hours);
        $this->assertNull($booking->end_date, 'end_date should stay null for an hours-mode booking.');
        $this->assertEquals('12:00 PM', $booking->formatted_start_time);
        $this->assertStringContainsString('for 4 hours', $booking->schedule_summary);

        $menuItem->refresh();
        $this->assertEquals(8, $menuItem->stock, 'Stock was not decremented correctly.');

        $customer = Customer::where('email', 'jane@example.com')->first();
        $this->assertNotNull($customer, 'Customer record was not created.');
        $this->assertEquals($customer->id, $booking->customer_id);

        $this->assertEquals(0, CartItem::where('menu_item_id', $menuItem->id)->count(), 'Cart was not cleared.');
    }

    public function test_booking_rejects_order_exceeding_available_stock(): void
    {
        Mail::fake();

        $menuItem = MenuItem::create([
            'name' => 'Limited Chicken Pack',
            'price' => 100.00,
            'stock' => 5,
            'is_active' => true,
        ]);

        $addResponse = $this->post('/cart/add', [
            'menu_item_id' => $menuItem->id,
            'quantity' => 5,
        ]);
        $addResponse->assertSessionHasNoErrors();
        $this->carrySessionCookieForward($addResponse);

        // Simulate stock dropping (e.g. another customer ordered it) between
        // add-to-cart and booking — booking must still refuse to oversell.
        $menuItem->update(['stock' => 1]);

        $response = $this->post('/booking', [
            'customer_name' => 'John Smith',
            'customer_email' => 'john@example.com',
            'customer_phone' => '+264811234568',
            'event_address' => '456 Independence Ave, Windhoek',
            'event_date' => now()->addDays(3)->toDateString(),
            'serving_period' => 'dinner',
            'duration_mode' => 'full_day',
            'guest_count' => 10,
            'event_type' => 'Corporate',
            'payment_method' => 'whatsapp',
        ]);

        $response->assertSessionHas('error', function ($message) {
            return str_contains($message, 'Insufficient stock');
        });
        $this->assertEquals(0, Booking::where('customer_email', 'john@example.com')->count());

        $menuItem->refresh();
        $this->assertEquals(1, $menuItem->stock, 'Stock should be unchanged on a rejected booking.');
    }

    public function test_booking_rejects_missing_required_event_details(): void
    {
        Mail::fake();

        $menuItem = MenuItem::create([
            'name' => 'Test Pack',
            'price' => 80.00,
            'stock' => 10,
            'is_active' => true,
        ]);

        $addResponse = $this->post('/cart/add', [
            'menu_item_id' => $menuItem->id,
            'quantity' => 1,
        ]);
        $this->carrySessionCookieForward($addResponse);

        $response = $this->post('/booking', [
            'customer_name' => 'Jane Doe',
            'customer_email' => 'jane@example.com',
            'customer_phone' => '+264811234567',
            'event_address' => '123 Independence Ave, Windhoek',
            // event_date, serving_period, duration_mode, guest_count, event_type deliberately omitted
            'payment_method' => 'whatsapp',
        ]);

        $response->assertSessionHasErrors(['event_date', 'serving_period', 'duration_mode', 'guest_count', 'event_type']);
        $this->assertEquals(0, Booking::count());
    }

    public function test_hours_mode_requires_start_time_and_duration(): void
    {
        Mail::fake();

        $menuItem = MenuItem::create(['name' => 'Test Pack', 'price' => 80.00, 'stock' => 10, 'is_active' => true]);
        $addResponse = $this->post('/cart/add', ['menu_item_id' => $menuItem->id, 'quantity' => 1]);
        $this->carrySessionCookieForward($addResponse);

        $response = $this->post('/booking', [
            'customer_name' => 'Jane Doe',
            'customer_email' => 'jane@example.com',
            'customer_phone' => '+264811234567',
            'event_address' => '123 Independence Ave, Windhoek',
            'event_date' => now()->addDays(3)->toDateString(),
            'serving_period' => 'lunch',
            'duration_mode' => 'hours',
            // start_time and duration_hours deliberately omitted
            'guest_count' => 5,
            'event_type' => 'Other',
            'payment_method' => 'whatsapp',
        ]);

        $response->assertSessionHasErrors(['start_time', 'duration_hours']);
        $this->assertEquals(0, Booking::count());
    }

    public function test_multi_day_mode_requires_valid_end_date_within_span_limit(): void
    {
        Mail::fake();

        $menuItem = MenuItem::create(['name' => 'Test Pack', 'price' => 80.00, 'stock' => 10, 'is_active' => true]);
        $addResponse = $this->post('/cart/add', ['menu_item_id' => $menuItem->id, 'quantity' => 1]);
        $this->carrySessionCookieForward($addResponse);

        $eventDate = now()->addDays(3)->toDateString();

        // Missing end_date entirely.
        $missing = $this->post('/booking', [
            'customer_name' => 'Jane Doe',
            'customer_email' => 'jane@example.com',
            'customer_phone' => '+264811234567',
            'event_address' => '123 Independence Ave, Windhoek',
            'event_date' => $eventDate,
            'serving_period' => 'lunch',
            'duration_mode' => 'multi_day',
            'guest_count' => 5,
            'event_type' => 'Other',
            'payment_method' => 'whatsapp',
        ]);
        $missing->assertSessionHasErrors(['end_date']);

        // end_date far beyond the 30-day span cap.
        $tooLong = $this->post('/booking', [
            'customer_name' => 'Jane Doe',
            'customer_email' => 'jane@example.com',
            'customer_phone' => '+264811234567',
            'event_address' => '123 Independence Ave, Windhoek',
            'event_date' => $eventDate,
            'serving_period' => 'lunch',
            'duration_mode' => 'multi_day',
            'end_date' => now()->addDays(3)->addDays(45)->toDateString(),
            'guest_count' => 5,
            'event_type' => 'Other',
            'payment_method' => 'whatsapp',
        ]);
        $tooLong->assertSessionHasErrors(['end_date']);

        // A valid 3-day span succeeds and is stored correctly.
        $endDate = now()->addDays(3)->addDays(2)->toDateString();
        $ok = $this->post('/booking', [
            'customer_name' => 'Jane Doe',
            'customer_email' => 'jane@example.com',
            'customer_phone' => '+264811234567',
            'event_address' => '123 Independence Ave, Windhoek',
            'event_date' => $eventDate,
            'serving_period' => 'lunch',
            'duration_mode' => 'multi_day',
            'end_date' => $endDate,
            'guest_count' => 5,
            'event_type' => 'Other',
            'payment_method' => 'whatsapp',
        ]);
        $ok->assertRedirect();

        $booking = Booking::where('customer_email', 'jane@example.com')->first();
        $this->assertNotNull($booking);
        $this->assertEquals('multi_day', $booking->duration_mode);
        $this->assertNull($booking->duration_hours, 'duration_hours should stay null for a multi-day booking.');
        $this->assertTrue($booking->end_date->isSameDay(\Illuminate\Support\Carbon::parse($endDate)));
        $this->assertStringContainsString(' to ', $booking->schedule_summary);
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
