<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index()
    {
        $baseUrl = config('app.url');
        $products = Product::where('is_active', true)->get();
        
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";
        
        // Homepage
        $xml .= $this->urlElement($baseUrl, '1.0', 'daily');
        
        // Products index
        $xml .= $this->urlElement($baseUrl . '/products', '0.9', 'daily');
        
        // Contact page
        $xml .= $this->urlElement($baseUrl . '/contact', '0.8', 'monthly');
        
        // Terms page
        $xml .= $this->urlElement($baseUrl . '/terms', '0.5', 'yearly');
        $xml .= $this->urlElement($baseUrl . '/privacy', '0.5', 'yearly');
        $xml .= $this->urlElement($baseUrl . '/delivery', '0.6', 'monthly');
        $xml .= $this->urlElement($baseUrl . '/returns', '0.5', 'yearly');

        // Promotion page
        $xml .= $this->urlElement($baseUrl . '/promotion', '0.7', 'weekly');

        // Gallery page
        $xml .= $this->urlElement($baseUrl . '/gallery', '0.8', 'weekly');
        
        // Individual products
        foreach ($products as $product) {
            $url = $baseUrl . '/products/' . $product->id;
            $xml .= '  <url>' . "\n";
            $xml .= '    <loc>' . htmlspecialchars($url) . '</loc>' . "\n";
            $xml .= '    <lastmod>' . $product->updated_at->format('Y-m-d') . '</lastmod>' . "\n";
            $xml .= '    <changefreq>weekly</changefreq>' . "\n";
            $xml .= '    <priority>0.8</priority>' . "\n";
            if ($product->image) {
                $xml .= '    <image:image>' . "\n";
                $xml .= '      <image:loc>' . htmlspecialchars($product->image_url) . '</image:loc>' . "\n";
                $xml .= '      <image:title>' . htmlspecialchars($product->name) . '</image:title>' . "\n";
                $xml .= '      <image:caption>' . htmlspecialchars($product->description ?? $product->name) . '</image:caption>' . "\n";
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
