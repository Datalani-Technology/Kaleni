<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    private const PASSWORD = 'CorrectHorseBattery9!';

    public function test_legacy_admin_path_is_not_reachable(): void
    {
        $this->get('/admin/login')->assertNotFound();
    }

    public function test_login_goes_straight_to_dashboard(): void
    {
        $this->createAdmin();

        // No second factor — a correct password logs the session straight in
        // and the dashboard is immediately reachable.
        $this->post($this->loginUrl(), [
            'email' => 'admin@example.test',
            'password' => self::PASSWORD,
        ])->assertRedirect(route('admin.dashboard'));

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
