<?php

namespace App\Services;

class SeoService
{
    public static function getMetaTags($page = 'home', $data = [])
    {
        $brand = '/Namsa Florals';
        $defaults = [
            'title' => $brand . ' – Namibia\'s Premier Flower Shop | Same-Day Delivery Windhoek',
            'description' => 'Fresh flowers in Namibia. Same-day delivery Windhoek. Hand-picked bouquets, roses & arrangements. Order online from /Namsa Florals – best flower delivery Windhoek.',
            'keywords' => 'flowers namibia, flower delivery windhoek, buy flowers online namibia, flower shop namibia, roses namibia, bouquets namibia, fresh flowers namibia, flower arrangements namibia, online flowers namibia, namsa florals, namsa, Namsa Flora, /Namsa Florals',
            'image' => asset('images/og-image.jpg'),
            'url' => url()->current(),
            'type' => 'website',
        ];

        $meta = match($page) {
            'home' => [
                'title' => 'Welcome to /Namsa Florals – Namibia\'s Premier Flower Shop | Same-Day Delivery Windhoek',
                'description' => 'Your premier destination for beautiful fresh flowers in Namibia. Same-day delivery in Windhoek. Hand-picked bouquets, roses & arrangements. Order flowers online – best flower delivery Windhoek. Shop /Namsa Florals.',
                'keywords' => 'flowers namibia, flower delivery windhoek, buy flowers online namibia, flower shop namibia, same day delivery windhoek, fresh flowers namibia, roses namibia, bouquets namibia, namsa florals, order flowers online namibia, flower arrangements windhoek, best florist namibia, online flower shop windhoek',
                'abstract' => 'Welcome to /Namsa Florals – Namibia\'s Premier Flower Shop. Your premier destination for beautiful fresh flowers in Namibia | Same-day delivery in Windhoek. At /Namsa Florals, Namibia\'s leading online flower shop, we specialize in bringing the beauty of nature into your life. We offer a wide selection of fresh, hand-picked flowers perfect for any occasion — from romantic gestures to celebratory moments, from expressing sympathy to showing appreciation. As the best flower delivery service in Windhoek, Namibia, we ensure every bouquet is crafted with care and delivered fresh. Our commitment is to provide the freshest, most beautiful flowers in Namibia, carefully arranged and delivered with care. Elegant roses, vibrant mixed bouquets, exotic arrangements. Order flowers online in Namibia with confidence — premium quality flowers delivered to your door in Windhoek and surrounding areas.',
            ],
            'products' => [
                'title' => 'Buy Fresh Flowers Online in Namibia | ' . $brand . ' – Same-Day Windhoek',
                'description' => 'Fresh flowers in Namibia: roses, lilies, sunflowers, orchids, bouquets. Same-day delivery Windhoek. Order online – /Namsa Florals.',
                'keywords' => 'buy flowers namibia, online flower shop namibia, roses namibia, flower delivery windhoek, fresh flowers online namibia, flower arrangements namibia, namsa florals, bouquets windhoek',
            ],
            'product' => [
                'title' => ($data['name'] ?? 'Product') . ' – Buy Online | ' . $brand . ' Namibia',
                'description' => ($data['description'] ?? '') . ' Order online from ' . $brand . '. Same-day delivery in Windhoek, Namibia. Fresh flowers guaranteed.',
                'keywords' => ($data['name'] ?? '') . ', flowers namibia, buy flowers online, flower delivery windhoek, namsa florals, namsa',
                'image' => isset($data['image']) ? asset('storage/' . $data['image']) : $defaults['image'],
                'type' => 'product',
            ],
            'contact' => [
                'title' => 'Contact Us – ' . $brand . ' Flower Shop Namibia | Windhoek',
                'description' => 'Contact /Namsa Florals. Fresh flower delivery Windhoek & Namibia. Order online, call, or visit. Same-day delivery.',
                'keywords' => 'contact namsa florals, flower shop contact namibia, flower delivery contact windhoek, namsa florals phone number, namsa',
            ],
            'terms' => [
                'title' => 'Terms and Conditions – ' . $brand . ' Namibia',
                'description' => 'Terms for flower orders and delivery in Namibia. Payment, delivery, returns. /Namsa Florals.',
                'keywords' => 'terms and conditions namsa florals, flower shop terms namibia, namsa',
            ],
            'promotion' => [
                'title' => 'Promotions & Specials – ' . $brand . ' | Flower Deals Windhoek',
                'description' => 'Flower specials and promotions from /Namsa Florals. Download our catalog. Fresh flowers, same-day delivery Windhoek.',
                'keywords' => 'flower specials namibia, flower promotions windhoek, namsa florals offers, flower catalog namibia, namsa',
            ],
            'gallery' => [
                'title' => 'Gallery – Flowers, Experiences & More | ' . $brand . ' Namibia',
                'description' => 'Browse our flower gallery: client moments, arrangements, experiences and more. /Namsa Florals – fresh flowers Windhoek & Namibia.',
                'keywords' => 'flower gallery namibia, namsa florals gallery, flower photos windhoek, flower arrangements namibia, namsa',
            ],
            default => $defaults,
        };

        return array_merge($defaults, $meta, $data);
    }

    public static function generateStructuredData($type, $data = [])
    {
        $baseUrl = config('app.url');
        $brand = '/Namsa Florals';
        $alternateNames = ['Namsa Flora', 'namsa', 'namsa florals'];

        return match($type) {
            'organization' => [
                '@context' => 'https://schema.org',
                '@type' => 'Florist',
                'name' => $brand,
                'alternateName' => $alternateNames,
                'description' => 'Welcome to /Namsa Florals, Namibia\'s premier flower shop. Your premier destination for beautiful fresh flowers in Namibia with same-day delivery in Windhoek. We specialize in bringing the beauty of nature into your life. We offer a wide selection of fresh, hand-picked flowers perfect for any occasion — from romantic gestures to celebratory moments, from expressing sympathy to showing appreciation. As the best flower delivery service in Windhoek, Namibia, we ensure every bouquet is crafted with care and delivered fresh. Our commitment is to provide the freshest, most beautiful flowers in Namibia, carefully arranged and delivered with care. Elegant roses, vibrant mixed bouquets, and exotic arrangements. Order flowers online in Namibia with confidence — premium quality flowers delivered to your door in Windhoek and surrounding areas.',
                'url' => $baseUrl,
                'logo' => $baseUrl . '/images/logo.png',
                'image' => $baseUrl . '/images/og-image.jpg',
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
                'sameAs' => [
                    config('social.facebook'),
                    config('social.instagram'),
                    config('social.tiktok'),
                ],
            ],
            'product' => [
                '@context' => 'https://schema.org',
                '@type' => 'Product',
                'name' => $data['name'] ?? '',
                'description' => $data['description'] ?? '',
                'image' => isset($data['image']) ? $baseUrl . '/storage/' . $data['image'] : '',
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
                'description' => 'Your premier destination for beautiful fresh flowers in Namibia. Same-day delivery in Windhoek. Hand-picked bouquets, roses and arrangements. Order flowers online — best flower delivery Windhoek. /Namsa Florals.',
                'url' => $baseUrl,
                'potentialAction' => [
                    '@type' => 'SearchAction',
                    'target' => $baseUrl . '/products?search={search_term_string}',
                    'query-input' => 'required name=search_term_string',
                ],
            ],
            'webpage_home' => [
                '@context' => 'https://schema.org',
                '@type' => 'WebPage',
                'name' => 'Welcome to /Namsa Florals – Namibia\'s Premier Flower Shop | Same-Day Delivery Windhoek',
                'description' => 'Your premier destination for beautiful fresh flowers in Namibia. Same-day delivery in Windhoek. At /Namsa Florals, Namibia\'s leading online flower shop, we specialize in bringing the beauty of nature into your life. We offer a wide selection of fresh, hand-picked flowers perfect for any occasion — from romantic gestures to celebratory moments, from expressing sympathy to showing appreciation. As the best flower delivery service in Windhoek, Namibia, we ensure every bouquet is crafted with care and delivered fresh. Our commitment is to provide the freshest, most beautiful flowers in Namibia, carefully arranged and delivered with care. Whether you\'re looking for elegant roses, vibrant mixed bouquets, or exotic arrangements, we have something special for everyone. Order flowers online in Namibia with confidence, knowing you\'re getting premium quality flowers delivered to your door in Windhoek and surrounding areas.',
                'url' => $baseUrl,
            ],
            default => [],
        };
    }
}
