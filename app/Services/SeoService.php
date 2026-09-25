<?php

namespace App\Services;

class SeoService
{
    public static function getMetaTags($page = 'home', $data = [])
    {
        $brand = 'Kaleni Catering Services';
        $defaults = [
            'title' => $brand . ' | Home-Style Catering in Windhoek, Namibia',
            'description' => 'Kaleni Catering Services (Chef K): lunch and dinner packs, event catering, and a new Food of the Day in Windhoek, Namibia. Book your event or order a pack today.',
            'keywords' => 'catering namibia, catering windhoek, kaleni catering, chef k, lunch packs windhoek, dinner packs windhoek, event catering namibia, food of the day windhoek, braai catering namibia',
            'image' => asset('images/kaleni/brand/kaleni-logo.jpg'),
            'url' => url()->current(),
            'type' => 'website',
        ];

        $meta = match($page) {
            'home' => [
                'title' => 'Kaleni Catering Services | Home-Style Catering, Lunch and Dinner Packs in Windhoek',
                'description' => 'Kaleni Catering Services (Chef K) brings home-style Namibian catering to Windhoek: lunch and dinner packs, a new Food of the Day, event catering and platters. Book online today.',
                'keywords' => 'kaleni catering, chef k, catering windhoek, lunch packs namibia, dinner packs namibia, food of the day, event catering windhoek, braai platters namibia',
                'abstract' => 'Kaleni Catering Services: home-style Namibian catering in Windhoek. We serve fresh lunch and dinner packs, a different Food of the Day, and full event catering for weddings, corporate functions, birthdays and more. Chicken, braai platters, sides, wraps and packages made the way home cooking should taste, delivered around Windhoek. Book your event or order a pack from Kaleni Catering Services (Chef K) today.',
            ],
            'products', 'menu' => [
                'title' => 'Menu: Lunch and Dinner Packs, Platters | ' . $brand,
                'description' => 'Browse the Kaleni Catering menu: lunch packs, dinner packs, braai platters, sides and wraps. Order for pickup/delivery or book us for your event in Windhoek, Namibia.',
                'keywords' => 'kaleni catering menu, lunch pack windhoek, dinner pack windhoek, braai platter namibia, catering menu namibia',
            ],
            'product' => [
                'title' => ($data['name'] ?? 'Menu item') . ' | ' . $brand . ' Windhoek',
                'description' => ($data['description'] ?? '') . ' Order online from ' . $brand . ' in Windhoek, Namibia.',
                'keywords' => ($data['name'] ?? '') . ', kaleni catering, catering windhoek, catering namibia',
                'image' => $data['image_url'] ?? (isset($data['image']) ? asset('storage/' . $data['image']) : $defaults['image']),
                'type' => 'product',
            ],
            'contact' => [
                'title' => 'Contact Us | ' . $brand . ', Windhoek, Namibia',
                'description' => 'Contact Kaleni Catering Services (Chef K) to book your event or place an order. Free delivery around Windhoek.',
                'keywords' => 'contact kaleni catering, chef k contact, catering booking windhoek, kaleni catering phone number',
            ],
            'terms' => [
                'title' => 'Terms and Conditions | ' . $brand,
                'description' => 'Terms for catering bookings and orders from Kaleni Catering Services in Windhoek, Namibia.',
                'keywords' => 'terms and conditions kaleni catering, catering terms namibia',
            ],
            'privacy' => [
                'title' => 'Privacy Policy | ' . $brand,
                'description' => 'How Kaleni Catering Services collects, uses, and protects your information when you book or order online.',
                'keywords' => 'privacy policy kaleni catering, catering privacy namibia',
            ],
            'delivery' => [
                'title' => 'Delivery and Event Setup | ' . $brand . ', Windhoek',
                'description' => 'Delivery areas, drop-off windows, and on-site setup for Kaleni Catering Services bookings in Windhoek and surrounding areas, Namibia.',
                'keywords' => 'kaleni catering delivery, event catering setup windhoek, free delivery windhoek catering',
            ],
            'returns', 'cancellations' => [
                'title' => 'Cancellations and Refunds | ' . $brand,
                'description' => 'Our cancellation and refund policy for catering bookings and orders. Kaleni Catering Services, Windhoek, Namibia.',
                'keywords' => 'catering cancellation policy namibia, kaleni catering refund policy',
            ],
            'gallery' => [
                'title' => 'Gallery: Events, Platters and More | ' . $brand,
                'description' => 'Browse the Kaleni Catering gallery: event moments, platters, and behind-the-scenes from Chef K\'s kitchen in Windhoek, Namibia.',
                'keywords' => 'kaleni catering gallery, catering photos windhoek, chef k gallery',
            ],
            'food-of-the-day' => [
                'title' => 'Food of the Day | ' . $brand . ', Windhoek',
                'description' => 'See what Chef K is cooking today and this week. A different menu every day from Kaleni Catering Services, Windhoek.',
                'keywords' => 'food of the day windhoek, daily special catering namibia, kaleni catering today',
            ],
            'special-requests' => [
                'title' => 'Request a Special Item | ' . $brand,
                'description' => 'Don\'t see what you need on the menu? Tell Kaleni Catering Services what you have in mind and we\'ll quote you.',
                'keywords' => 'custom catering request namibia, bespoke catering windhoek, kaleni catering special order',
            ],
            default => $defaults,
        };

        return array_merge($defaults, $meta, $data);
    }

    public static function generateStructuredData($type, $data = [])
    {
        $baseUrl = config('app.url');
        $brand = 'Kaleni Catering Services';
        $alternateNames = ['Kaleni Catering', 'Chef K', 'Leni Kaleni'];

        return match($type) {
            'organization' => [
                '@context' => 'https://schema.org',
                '@type' => 'FoodEstablishment',
                'name' => $brand,
                'alternateName' => $alternateNames,
                'description' => 'Kaleni Catering Services (Chef K) is a home-style catering business in Windhoek, Namibia, offering lunch and dinner packs, a daily Food of the Day, event catering and bespoke special requests.',
                'url' => $baseUrl,
                'logo' => $baseUrl . '/images/kaleni/brand/kaleni-logo.jpg',
                'image' => $baseUrl . '/images/kaleni/brand/kaleni-logo.jpg',
                'address' => [
                    '@type' => 'PostalAddress',
                    'streetAddress' => 'Windhoek',
                    'addressLocality' => 'Windhoek',
                    'addressRegion' => 'Khomas',
                    'addressCountry' => 'NA',
                ],
                'contactPoint' => [
                    '@type' => 'ContactPoint',
                    'telephone' => config('contact.phone'),
                    'contactType' => 'customer service',
                    'areaServed' => ['Windhoek', 'NA'],
                    'availableLanguage' => ['English'],
                ],
                'sameAs' => array_values(array_filter([
                    config('social.facebook'),
                    config('social.instagram'),
                    config('social.tiktok'),
                ])),
            ],
            'product' => [
                '@context' => 'https://schema.org',
                '@type' => 'Product',
                'name' => $data['name'] ?? '',
                'description' => $data['description'] ?? '',
                'image' => $data['image_url'] ?? (isset($data['image']) ? $baseUrl . '/storage/' . $data['image'] : ''),
                'brand' => [
                    '@type' => 'Brand',
                    'name' => $brand,
                ],
                'offers' => [
                    '@type' => 'Offer',
                    'price' => $data['price'] ?? 0,
                    'priceCurrency' => 'NAD',
                    'availability' => ($data['stock'] ?? 0) > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                    'url' => $data['url'] ?? '',
                ],
            ],
            'breadcrumb' => [
                '@context' => 'https://schema.org',
                '@type' => 'BreadcrumbList',
                'itemListElement' => $data['items'] ?? [],
            ],
            'website' => [
                '@context' => 'https://schema.org',
                '@type' => 'WebSite',
                'name' => $brand,
                'alternateName' => $alternateNames,
                'description' => 'Home-style catering in Windhoek, Namibia. Lunch and dinner packs, a daily Food of the Day, and full event catering. Book online with Kaleni Catering Services (Chef K).',
                'url' => $baseUrl,
                'potentialAction' => [
                    '@type' => 'SearchAction',
                    'target' => $baseUrl . '/menu?search={search_term_string}',
                    'query-input' => 'required name=search_term_string',
                ],
            ],
            'webpage_home' => [
                '@context' => 'https://schema.org',
                '@type' => 'WebPage',
                'name' => 'Kaleni Catering Services | Home-Style Catering, Lunch and Dinner Packs in Windhoek',
                'description' => 'Kaleni Catering Services (Chef K) brings home-style Namibian catering to Windhoek: lunch and dinner packs, a new Food of the Day, event catering and platters. Book your event or order a pack today.',
                'url' => $baseUrl,
            ],
            default => [],
        };
    }
}
