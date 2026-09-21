<?php

namespace Tests\Feature;

use App\Mail\NewSpecialRequestAlertMail;
use App\Mail\SpecialRequestReceivedMail;
use App\Models\SpecialRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SpecialRequestTest extends TestCase
{
    use RefreshDatabase;

    private const PASSWORD = 'CorrectHorseBattery9!';

    public function test_guest_can_submit_a_special_request(): void
    {
        Mail::fake();

        $response = $this->post('/special-requests', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '+264811234567',
            'event_date' => now()->addDays(10)->toDateString(),
            'guest_count' => 50,
            'occasion' => 'Wedding',
            'details' => 'Full plated dinner for 50 guests, three courses.',
            'budget_range' => 'N$ 10,000 - N$ 15,000',
        ]);

        $response->assertRedirect(route('special-requests.create'));

        $this->assertDatabaseHas('special_requests', [
            'email' => 'jane@example.com',
            'status' => 'new',
        ]);

        Mail::assertSent(NewSpecialRequestAlertMail::class);
        Mail::assertSent(SpecialRequestReceivedMail::class);
    }

    public function test_special_request_requires_details(): void
    {
        Mail::fake();

        $response = $this->post('/special-requests', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '+264811234567',
            'details' => '',
        ]);

        $response->assertSessionHasErrors('details');
        $this->assertEquals(0, SpecialRequest::count());
    }

    public function test_admin_can_update_special_request_status_and_quote(): void
    {
        $admin = User::create([
            'name' => 'Test Admin',
            'email' => 'admin@example.test',
            'password' => Hash::make(self::PASSWORD),
            'role' => 'admin',
        ]);

        $specialRequest = SpecialRequest::create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '+264811234567',
            'details' => 'Full plated dinner for 50 guests.',
            'status' => 'new',
        ]);

        $this->post('/' . config('admin.path') . '/login', [
            'email' => $admin->email,
            'password' => self::PASSWORD,
        ])->assertRedirect(route('admin.dashboard'));

        $response = $this->post(route('admin.special-requests.update', $specialRequest), [
            'status' => 'quoted',
            'quoted_amount' => 12000,
            'admin_notes' => 'Quoted via phone.',
        ]);

        $response->assertRedirect(route('admin.special-requests.show', $specialRequest));

        $specialRequest->refresh();
        $this->assertEquals('quoted', $specialRequest->status);
        $this->assertEquals(12000, (float) $specialRequest->quoted_amount);
        $this->assertNotNull($specialRequest->responded_at);
    }
}
