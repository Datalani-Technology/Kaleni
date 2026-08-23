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
                'is_featured' => true,
                'image' => 'site:catalog/namsa-red-rose-v2.webp',
            ],
            [
                'name' => 'Mixed Flower Arrangement',
                'description' => 'An elegant arrangement of mixed seasonal flowers including roses, lilies, and carnations. Perfect for any occasion.',
                'price' => 350.00,
                'stock' => 30,
                'category' => 'Bouquets',
                'is_active' => true,
                'is_featured' => true,
                'image' => 'site:catalog/namsa-mixed-arrangement-v2.webp',
            ],
            [
                'name' => 'White Lily Bouquet',
                'description' => 'Pure white lilies arranged in a stunning bouquet. Symbolizes purity and elegance. Great for weddings and special events.',
                'price' => 280.00,
                'stock' => 25,
                'category' => 'Lilies',
                'is_active' => true,
                'is_featured' => false,
                'image' => 'site:catalog/namsa-white-lilies-v2.webp',
            ],
            [
                'name' => 'Sunflower Bouquet',
                'description' => 'Bright and cheerful sunflowers that bring sunshine to any room. Perfect for birthdays and celebrations.',
                'price' => 200.00,
                'stock' => 40,
                'category' => 'Sunflowers',
                'is_active' => true,
                'is_featured' => true,
                'image' => 'site:catalog/namsa-sunflowers-v2.webp',
            ],
            [
                'name' => 'Orchid Plant',
                'description' => 'An elegant white phalaenopsis orchid in a charcoal ceramic pot. A lasting, sculptural gift for home or office.',
                'price' => 450.00,
                'stock' => 15,
                'category' => 'Plants',
                'is_active' => true,
                'is_featured' => true,
                'image' => 'site:catalog/namsa-orchid-v2.webp',
            ],
            [
                'name' => 'Pink Rose Bouquet',
                'description' => 'Delicate pink roses arranged with baby\'s breath. A romantic gesture perfect for anniversaries and special moments.',
                'price' => 270.00,
                'stock' => 35,
                'category' => 'Roses',
                'is_active' => true,
                'is_featured' => false,
                'image' => 'site:catalog/namsa-pink-rose-v2.webp',
            ],
            [
                'name' => 'Tulip Bouquet',
                'description' => 'Colorful tulips in various shades. Fresh and vibrant, perfect for spring celebrations.',
                'price' => 220.00,
                'stock' => 28,
                'category' => 'Tulips',
                'is_active' => true,
                'is_featured' => false,
                'image' => 'site:catalog/namsa-tulips-v2.webp',
            ],
            [
                'name' => 'Carnation Arrangement',
                'description' => 'A lush bowl of wine, blush, and cream carnations with restrained eucalyptus. Rich in texture and made to last beautifully.',
                'price' => 180.00,
                'stock' => 45,
                'category' => 'Carnations',
                'is_active' => true,
                'is_featured' => false,
                'image' => 'site:catalog/namsa-carnations-v2.webp',
            ],
        ];

        foreach ($products as $productData) {
            $product = Product::where('name', $productData['name'])->oldest()->first();

            if ($product) {
                $product->update($productData);
            } else {
                $product = Product::create($productData);
            }

            Product::where('name', $productData['name'])
                ->where('id', '!=', $product->id)
                ->update(['is_active' => false, 'is_featured' => false]);
        }
    }
}
