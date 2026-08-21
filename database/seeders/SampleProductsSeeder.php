<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class SampleProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Red Rose Bouquet',
                'description' => 'A beautiful bouquet of 12 fresh red roses, perfect for expressing your love and affection. Each rose is carefully selected and arranged with care.',
                'price' => 250.00,
                'stock' => 50,
                'category' => 'Roses',
                'is_active' => true,
            ],
            [
                'name' => 'Mixed Flower Arrangement',
                'description' => 'An elegant arrangement of mixed seasonal flowers including roses, lilies, and carnations. Perfect for any occasion.',
                'price' => 350.00,
                'stock' => 30,
                'category' => 'Bouquets',
                'is_active' => true,
            ],
            [
                'name' => 'White Lily Bouquet',
                'description' => 'Pure white lilies arranged in a stunning bouquet. Symbolizes purity and elegance. Great for weddings and special events.',
                'price' => 280.00,
                'stock' => 25,
                'category' => 'Lilies',
                'is_active' => true,
            ],
            [
                'name' => 'Sunflower Bouquet',
                'description' => 'Bright and cheerful sunflowers that bring sunshine to any room. Perfect for birthdays and celebrations.',
                'price' => 200.00,
                'stock' => 40,
                'category' => 'Sunflowers',
                'is_active' => true,
            ],
            [
                'name' => 'Orchid Plant',
                'description' => 'Exotic purple orchid plant in a decorative pot. A long-lasting gift that brings beauty to any space.',
                'price' => 450.00,
                'stock' => 15,
                'category' => 'Plants',
                'is_active' => true,
            ],
            [
                'name' => 'Pink Rose Bouquet',
                'description' => 'Delicate pink roses arranged with baby\'s breath. A romantic gesture perfect for anniversaries and special moments.',
                'price' => 270.00,
                'stock' => 35,
                'category' => 'Roses',
                'is_active' => true,
            ],
            [
                'name' => 'Tulip Bouquet',
                'description' => 'Colorful tulips in various shades. Fresh and vibrant, perfect for spring celebrations.',
                'price' => 220.00,
                'stock' => 28,
                'category' => 'Tulips',
                'is_active' => true,
            ],
            [
                'name' => 'Carnation Arrangement',
                'description' => 'Beautiful carnations in a variety of colors. Long-lasting flowers that stay fresh for weeks.',
                'price' => 180.00,
                'stock' => 45,
                'category' => 'Carnations',
                'is_active' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
