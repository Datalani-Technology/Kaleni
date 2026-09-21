<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class RobotsController extends Controller
{
    public function index()
    {
        $baseUrl = config('app.url');
        
        $robots = "User-agent: *\n";
        $robots .= "Allow: /\n";
        $robots .= "Disallow: /booking/\n";
        $robots .= "Disallow: /payment/\n";
        $robots .= "Disallow: /cart/\n";
        $robots .= "\n";
        $robots .= "Sitemap: {$baseUrl}/sitemap.xml\n";
        
        return response($robots, 200)
            ->header('Content-Type', 'text/plain');
    }
}
