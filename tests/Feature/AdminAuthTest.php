<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    private const PASSWORD = 'CorrectHorseBattery9!';

    public function test_legacy_admin_path_is_not_reachable(): void
    {
        $this->get('/admin/login')->assertNotFound();
    }

    public function test_login_without_two_factor_forces_setup_before_dashboard(): void
    {
        $this->createAdmin();

        $this->post($this->loginUrl(), [
            'email' => 'admin@example.test',
            'password' => self::PASSWORD,
        ])->assertRedirect(route('admin.dashboard'));

        // Middleware should intercept and bounce to 2FA setup — dashboard content
        // must never be reachable before 2FA is confirmed.
        $this->get(route('admin.dashboard'))->assertRedirect(route('admin.2fa.setup'));
    }

    public function test_login_with_two_factor_requires_a_valid_code(): void
    {
        $secret = (new Google2FA())->generateSecretKey();
        $this->createAdmin([
            'two_factor_secret' => $secret,
            'two_factor_confirmed_at' => now(),
        ]);

        // Correct password alone must land on the 2FA challenge, not the
        // dashboard — proves the session isn't authenticated until the second
        // factor passes (Auth::validate(), not Auth::attempt(), in AuthController).
        $this->post($this->loginUrl(), [
            'email' => 'admin@example.test',
            'password' => self::PASSWORD,
        ])->assertRedirect(route('admin.2fa.challenge'));
        $this->assertGuest();

        $this->post(route('admin.2fa.challenge.verify'), ['code' => '000000'])
            ->assertSessionHasErrors('code');

        $validCode = (new Google2FA())->getCurrentOtp($secret);
        $this->post(route('admin.2fa.challenge.verify'), ['code' => $validCode])
            ->assertRedirect(route('admin.dashboard'));

        $this->get(route('admin.dashboard'))->assertOk();
    }

    public function test_account_locks_after_five_failed_password_attempts(): void
    {
        $this->createAdmin();

        for ($i = 0; $i < 5; $i++) {
            $this->post($this->loginUrl(), [
                'email' => 'admin@example.test',
                'password' => 'wrong-password',
            ]);
        }

        $response = $this->post($this->loginUrl(), [
            'email' => 'admin@example.test',
            'password' => self::PASSWORD,
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertNotNull(User::where('email', 'admin@example.test')->first()->locked_until);
    }

    private function createAdmin(array $overrides = []): User
    {
        return User::create(array_merge([
            'name' => 'Test Admin',
            'email' => 'admin@example.test',
            'password' => Hash::make(self::PASSWORD),
            'role' => 'admin',
        ], $overrides));
    }

    private function loginUrl(): string
    {
        return '/' . config('admin.path') . '/login';
    }
}
