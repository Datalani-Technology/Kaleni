<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class SampleMenuItemsSeeder extends Seeder
{
    /**
     * Seed the standing Kaleni Catering Services menu — real dishes, priced
     * as reasonable Windhoek catering rates. Adjust prices/stock freely from
     * the admin panel; this just gives the platform real content on launch.
     */
    public function run(): void
    {
        $menuItems = [
            [
                'name' => 'Grilled Chicken & Boerewors Pack',
                'description' => 'Flame-grilled chicken quarters with boerewors, creamy macaroni salad, corn on the cob and a wedge of lemon. Chef K\'s everyday lunch/dinner pack.',
                'price' => 120.00,
                'unit_label' => 'per pack',
                'serves_count' => 1,
                'stock' => 40,
                'category' => 'Lunch & Dinner Packs',
                'is_active' => true,
                'is_featured' => true,
                'image' => 'site:kaleni/menu/grilled-chicken-boerewors-pack.png',
            ],
            [
                'name' => 'Braai Platter',
                'description' => 'A sharing platter of grilled chops, boerewors, corn on the cob and macaroni salad with fresh chakalaka — built for groups and events.',
                'price' => 450.00,
                'unit_label' => 'per platter',
                'serves_count' => 5,
                'stock' => 15,
                'category' => 'Sharing Platters',
                'is_active' => true,
                'is_featured' => true,
                'image' => 'site:kaleni/menu/braai-platter.png',
            ],
            [
                'name' => 'Fried Fish Plate',
                'description' => 'A whole fried bream, served with pap, wild spinach (omboga) and fresh chakalaka.',
                'price' => 100.00,
                'unit_label' => 'per plate',
                'serves_count' => 1,
                'stock' => 25,
                'category' => 'Individual Meals',
                'is_active' => true,
                'is_featured' => true,
                'image' => 'site:kaleni/menu/fried-fish-plate.png',
            ],
            [
                'name' => 'Beef Stew Rice Box',
                'description' => 'Tender beef stewed in a rich tomato and onion gravy, served over rice with creamy mash and a side of vegetables.',
                'price' => 95.00,
                'unit_label' => 'per box',
                'serves_count' => 1,
                'stock' => 30,
                'category' => 'Individual Meals',
                'is_active' => true,
                'is_featured' => false,
                'image' => 'site:kaleni/menu/beef-stew-rice-box.png',
            ],
            [
                'name' => 'Grilled Chicken & Pasta Salad',
                'description' => 'Grilled chicken drumsticks and wings over tri-colour pasta salad with fresh carrots, peppers and courgette.',
                'price' => 95.00,
                'unit_label' => 'per pack',
                'serves_count' => 1,
                'stock' => 30,
                'category' => 'Lunch & Dinner Packs',
                'is_active' => true,
                'is_featured' => false,
                'image' => 'site:kaleni/menu/grilled-chicken-pasta-salad.png',
            ],
            [
                'name' => 'Chicken Feet & Wild Spinach',
                'description' => 'Slow-cooked chicken feet and a chicken quarter in tomato gravy, served with wild spinach (omboga) and pap — a Kaleni home-style favourite.',
                'price' => 85.00,
                'unit_label' => 'per plate',
                'serves_count' => 1,
                'stock' => 20,
                'category' => 'Traditional',
                'is_active' => true,
                'is_featured' => false,
                'image' => 'site:kaleni/menu/chicken-feet-wild-spinach.png',
            ],
            [
                'name' => 'Oxtail Stew with Pap',
                'description' => 'Oxtail slow-braised in a rich gravy, served with pap, wild spinach and corn on the cob.',
                'price' => 110.00,
                'unit_label' => 'per plate',
                'serves_count' => 1,
                'stock' => 20,
                'category' => 'Traditional',
                'is_active' => true,
                'is_featured' => true,
                'image' => 'site:kaleni/menu/oxtail-stew-pap.png',
            ],
            [
                'name' => 'Tripe Stew with Pap',
                'description' => 'Tender tripe (mogodu) simmered in a spiced tomato sauce, served with pap.',
                'price' => 90.00,
                'unit_label' => 'per plate',
                'serves_count' => 1,
                'stock' => 20,
                'category' => 'Traditional',
                'is_active' => true,
                'is_featured' => false,
                'image' => 'site:kaleni/menu/tripe-stew-pap.png',
            ],
        ];

        foreach ($menuItems as $data) {
            MenuItem::updateOrCreate(['name' => $data['name']], $data);
        }
    }
}
