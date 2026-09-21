<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;

class SitemapController extends Controller
{
    public function index()
    {
        $baseUrl = config('app.url');
        $menuItems = MenuItem::where('is_active', true)->get();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";

        // Homepage
        $xml .= $this->urlElement($baseUrl, '1.0', 'daily');

        // Menu index
        $xml .= $this->urlElement($baseUrl . '/menu', '0.9', 'daily');

        // Food of the day
        $xml .= $this->urlElement($baseUrl . '/food-of-the-day', '0.8', 'daily');

        // Special requests
        $xml .= $this->urlElement($baseUrl . '/special-requests', '0.7', 'monthly');

        // Contact page
        $xml .= $this->urlElement($baseUrl . '/contact', '0.8', 'monthly');

        // Terms page
        $xml .= $this->urlElement($baseUrl . '/terms', '0.5', 'yearly');
        $xml .= $this->urlElement($baseUrl . '/privacy', '0.5', 'yearly');
        $xml .= $this->urlElement($baseUrl . '/delivery', '0.6', 'monthly');
        $xml .= $this->urlElement($baseUrl . '/cancellations', '0.5', 'yearly');

        // Promotion page
        $xml .= $this->urlElement($baseUrl . '/promotion', '0.7', 'weekly');

        // Gallery page
        $xml .= $this->urlElement($baseUrl . '/gallery', '0.8', 'weekly');

        // Individual menu items
        foreach ($menuItems as $menuItem) {
            $url = $baseUrl . '/menu/' . $menuItem->id;
            $xml .= '  <url>' . "\n";
            $xml .= '    <loc>' . htmlspecialchars($url) . '</loc>' . "\n";
            $xml .= '    <lastmod>' . $menuItem->updated_at->format('Y-m-d') . '</lastmod>' . "\n";
            $xml .= '    <changefreq>weekly</changefreq>' . "\n";
            $xml .= '    <priority>0.8</priority>' . "\n";
            if ($menuItem->image) {
                $xml .= '    <image:image>' . "\n";
                $xml .= '      <image:loc>' . htmlspecialchars($menuItem->image_url) . '</image:loc>' . "\n";
                $xml .= '      <image:title>' . htmlspecialchars($menuItem->name) . '</image:title>' . "\n";
                $xml .= '      <image:caption>' . htmlspecialchars($menuItem->description ?? $menuItem->name) . '</image:caption>' . "\n";
                $xml .= '    </image:image>' . "\n";
            }
            $xml .= '  </url>' . "\n";
        }

        $xml .= '</urlset>';

        return response($xml, 200)
            ->header('Content-Type', 'application/xml');
    }

    private function urlElement($url, $priority, $changefreq)
    {
        return '  <url>' . "\n" .
               '    <loc>' . htmlspecialchars($url) . '</loc>' . "\n" .
               '    <lastmod>' . date('Y-m-d') . '</lastmod>' . "\n" .
               '    <changefreq>' . $changefreq . '</changefreq>' . "\n" .
               '    <priority>' . $priority . '</priority>' . "\n" .
               '  </url>' . "\n";
    }
}
