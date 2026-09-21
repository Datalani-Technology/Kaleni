<?php

namespace Database\Seeders;

use App\Models\GalleryItem;
use Illuminate\Database\Seeder;

class GalleryContentSeeder extends Seeder
{
    /**
     * Seeds the public gallery. Items with a title/description are real Kaleni
     * Catering Services photos of our own kitchen output. The untitled "stock"
     * items are licensed stock photography used purely as generic occasion
     * styling, never captioned as if they were a real Kaleni event — see
     * GalleryItem::OCCASIONS and the admin form's guidance.
     */
    public function run(): void
    {
        $items = [
            [
                'image' => 'site:kaleni/gallery/braai-platter-spread.png',
                'title' => 'Braai platter, ready to serve',
                'description' => 'Grilled chops, boerewors, corn and macaroni salad, plated for a group booking.',
                'occasion' => 'Braai',
                'sort_order' => 1,
            ],
            [
                'image' => 'site:kaleni/gallery/stock-buffet-chafing-dishes.jpg',
                'title' => null,
                'description' => null,
                'occasion' => 'Wedding',
                'sort_order' => 2,
            ],
            [
                'image' => 'site:kaleni/gallery/pap-and-sides-spread.png',
                'title' => 'Pap and sides, table-ready',
                'description' => 'Oxtail, corn and wild spinach with pap, boxed up for delivery.',
                'occasion' => null,
                'sort_order' => 3,
            ],
            [
                'image' => 'site:kaleni/gallery/stock-corporate-lunch-boxes.jpg',
                'title' => null,
                'description' => null,
                'occasion' => 'Corporate',
                'sort_order' => 4,
            ],
            [
                'image' => 'site:kaleni/gallery/tripe-boxes-spread.png',
                'title' => 'Tripe stew, packed for pickup',
                'description' => 'A batch of tripe stew, simmered and ready to go out to clients.',
                'occasion' => null,
                'sort_order' => 5,
            ],
            [
                'image' => 'site:kaleni/gallery/stock-braai-grill-flames.jpg',
                'title' => null,
                'description' => null,
                'occasion' => 'Braai',
                'sort_order' => 6,
            ],
            [
                'image' => 'site:kaleni/gallery/stock-birthday-celebration-table.jpg',
                'title' => null,
                'description' => null,
                'occasion' => 'Birthday',
                'sort_order' => 7,
            ],
            [
                'image' => 'site:kaleni/gallery/stock-corporate-outdoor-spread.jpg',
                'title' => null,
                'description' => null,
                'occasion' => 'Corporate',
                'sort_order' => 8,
            ],
            [
                'image' => 'site:kaleni/gallery/stock-wedding-outdoor-tablescape.jpg',
                'title' => null,
                'description' => null,
                'occasion' => 'Wedding',
                'sort_order' => 9,
            ],
        ];

        foreach ($items as $item) {
            GalleryItem::updateOrCreate(['image' => $item['image']], $item);
        }
    }
}
