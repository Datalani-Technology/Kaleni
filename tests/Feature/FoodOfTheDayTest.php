<?php

namespace Tests\Feature;

use App\Models\FoodOfTheDay;
use App\Models\MenuItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class FoodOfTheDayTest extends TestCase
{
    use RefreshDatabase;

    private const PASSWORD = 'CorrectHorseBattery9!';

    public function test_home_page_shows_todays_active_special(): void
    {
        $menuItem = MenuItem::create([
            'name' => 'Braai Platter',
            'price' => 450.00,
            'stock' => 10,
            'is_active' => true,
        ]);

        FoodOfTheDay::create([
            'menu_item_id' => $menuItem->id,
            'serve_date' => Carbon::today()->toDateString(),
            'is_active' => true,
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Braai Platter');
        $response->assertSee('Food of the Day');
    }

    public function test_home_page_degrades_gracefully_with_no_special_scheduled(): void
    {
        $response = $this->get('/');

        // No Food of the Day scheduled for today: the whole section is
        // skipped rather than showing an empty placeholder card. Check for
        // the section's own markup, not the nav link of the same name.
        $response->assertOk();
        $response->assertDontSee('id="fotd-title"', false);
    }

    public function test_inactive_special_is_not_shown(): void
    {
        $menuItem = MenuItem::create([
            'name' => 'Hidden Dish',
            'price' => 90.00,
            'stock' => 10,
            'is_active' => true,
        ]);

        FoodOfTheDay::create([
            'menu_item_id' => $menuItem->id,
            'serve_date' => Carbon::today()->toDateString(),
            'is_active' => false,
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertDontSee('Hidden Dish');
    }

    public function test_food_of_the_day_page_lists_the_week_ahead(): void
    {
        $menuItem = MenuItem::create([
            'name' => 'Oxtail Stew',
            'price' => 110.00,
            'stock' => 10,
            'is_active' => true,
        ]);

        FoodOfTheDay::create([
            'menu_item_id' => $menuItem->id,
            'serve_date' => Carbon::today()->addDays(2)->toDateString(),
            'is_active' => true,
        ]);

        $response = $this->get('/food-of-the-day');

        $response->assertOk();
        $response->assertSee('Oxtail Stew');
    }

    public function test_admin_can_schedule_a_menu_item_as_food_of_the_day(): void
    {
        $admin = User::create([
            'name' => 'Test Admin',
            'email' => 'admin@example.test',
            'password' => Hash::make(self::PASSWORD),
            'role' => 'admin',
        ]);

        $menuItem = MenuItem::create([
            'name' => 'Fried Fish Plate',
            'price' => 100.00,
            'stock' => 10,
            'is_active' => true,
        ]);

        $this->authenticateAdmin($admin);

        $response = $this->post(route('admin.food-of-the-day.store'), [
            'menu_item_id' => $menuItem->id,
            'serve_date' => Carbon::tomorrow()->toDateString(),
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('admin.food-of-the-day.index'));
        $this->assertTrue(
            FoodOfTheDay::where('menu_item_id', $menuItem->id)
                ->whereDate('serve_date', Carbon::tomorrow())
                ->exists()
        );
    }

    private function authenticateAdmin(User $admin): void
    {
        $this->post('/' . config('admin.path') . '/login', [
            'email' => $admin->email,
            'password' => self::PASSWORD,
        ])->assertRedirect(route('admin.dashboard'));
    }
}
