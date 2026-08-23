<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $catalog = [
            'Red Rose Bouquet' => ['image' => 'site:catalog/namsa-red-rose-v2.webp', 'is_featured' => true],
            'Mixed Flower Arrangement' => ['image' => 'site:catalog/namsa-mixed-arrangement-v2.webp', 'is_featured' => true],
            'White Lily Bouquet' => ['image' => 'site:catalog/namsa-white-lilies-v2.webp', 'is_featured' => false],
            'Sunflower Bouquet' => ['image' => 'site:catalog/namsa-sunflowers-v2.webp', 'is_featured' => true],
            'Orchid Plant' => ['image' => 'site:catalog/namsa-orchid-v2.webp', 'is_featured' => true],
            'Pink Rose Bouquet' => ['image' => 'site:catalog/namsa-pink-rose-v2.webp', 'is_featured' => false],
            'Tulip Bouquet' => ['image' => 'site:catalog/namsa-tulips-v2.webp', 'is_featured' => false],
            'Carnation Arrangement' => ['image' => 'site:catalog/namsa-carnations-v2.webp', 'is_featured' => false],
        ];

        DB::transaction(function () use ($catalog) {
            foreach ($catalog as $name => $attributes) {
                $products = DB::table('products')->where('name', $name)->orderBy('id')->get();
                $keeper = $products->first();

                if (!$keeper) {
                    continue;
                }

                DB::table('products')->where('id', $keeper->id)->update([
                    'image' => $attributes['image'],
                    'is_active' => true,
                    'is_featured' => $attributes['is_featured'],
                    'updated_at' => now(),
                ]);

                $duplicateIds = $products->skip(1)->pluck('id');
                if ($duplicateIds->isNotEmpty()) {
                    DB::table('products')->whereIn('id', $duplicateIds)->update([
                        'is_active' => false,
                        'is_featured' => false,
                        'updated_at' => now(),
                    ]);
                }
            }

            DB::table('products')->where('name', 'Orchid Plant')->update([
                'description' => 'An elegant white phalaenopsis orchid in a charcoal ceramic pot. A lasting, sculptural gift for home or office.',
            ]);
            DB::table('products')->where('name', 'Carnation Arrangement')->update([
                'description' => 'A lush bowl of wine, blush, and cream carnations with restrained eucalyptus. Rich in texture and made to last beautifully.',
            ]);

            $firstGalleryItem = DB::table('gallery_items')->orderBy('id')->first();
            if ($firstGalleryItem) {
                DB::table('gallery_items')->where('id', $firstGalleryItem->id)->update([
                    'title' => 'A celebration in Oshana',
                    'description' => 'A floral moment composed for a warm, joy-filled celebration.',
                    'sort_order' => 1,
                    'updated_at' => now(),
                ]);
            }

            $galleryItems = [
                ['image' => 'site:gallery/namsa-table-story-v2.webp', 'title' => 'The table, in bloom', 'description' => 'Wine, blush, and cream florals shaping an intimate celebration table.', 'sort_order' => 2],
                ['image' => 'site:gallery/namsa-florist-studio-v2.webp', 'title' => 'Made by hand', 'description' => 'Every stem considered, balanced, and tied in our studio.', 'sort_order' => 3],
                ['image' => 'site:gallery/namsa-orchid-installation-v2.webp', 'title' => 'A study in white', 'description' => 'Orchids and lilies arranged as a quiet sculptural statement.', 'sort_order' => 4],
            ];

            foreach ($galleryItems as $item) {
                DB::table('gallery_items')->updateOrInsert(
                    ['image' => $item['image']],
                    array_merge($item, ['updated_at' => now(), 'created_at' => now()])
                );
            }

            DB::table('settings')->where('key', 'promotion_pdf_path')->update([
                'value' => '',
                'updated_at' => now(),
            ]);
        });
    }

    public function down(): void
    {
        DB::table('gallery_items')->whereIn('image', [
            'site:gallery/namsa-table-story-v2.webp',
            'site:gallery/namsa-florist-studio-v2.webp',
            'site:gallery/namsa-orchid-installation-v2.webp',
        ])->delete();

        // Deliberately do not reactivate duplicate products or re-expose the removed customer document.
    }
};
