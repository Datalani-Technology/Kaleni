<?php

namespace Database\Seeders;

use App\Models\FoodOfTheDay;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class FoodOfTheDaySeeder extends Seeder
{
    /**
     * Seeds today's Food of the Day so the homepage has something to show
     * right after a fresh install. Admins can reschedule this from the panel.
     */
    public function run(): void
    {
        $menuItem = MenuItem::where('name', 'Grilled Chicken & Boerewors Pack')->first();

        if (!$menuItem) {
            return;
        }

        FoodOfTheDay::updateOrCreate(
            ['serve_date' => Carbon::today()->toDateString()],
            [
                'menu_item_id' => $menuItem->id,
                'is_active' => true,
            ]
        );
    }
}
