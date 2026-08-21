<?php

namespace App\Http\Middleware;

use App\Models\Visit;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class VisitTrackerMiddleware
{
    private const THROTTLE_MINUTES = 5;

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (!$this->shouldTrack($request)) {
            return $response;
        }

        $this->track($request);

        return $response;
    }

    private function shouldTrack(Request $request): bool
    {
        if ($request->method() !== 'GET') {
            return false;
        }
        if ($request->ajax() || $request->wantsJson()) {
            return false;
        }
        $path = $request->path();
        if (str_starts_with($path, 'admin')) {
            return false;
        }
        if (in_array($path, ['sitemap.xml', 'robots.txt'], true)) {
            return false;
        }
        if (str_starts_with($path, 'payment/')) {
            return false;
        }
        if (preg_match('#^checkout/success/#', $path)) {
            return false;
        }
        $segments = explode('/', trim($path, '/'));
        $first = $segments[0] ?? '';
        $tracked = ['', 'products', 'contact', 'terms', 'cart', 'checkout'];
        if (!in_array($first, $tracked, true)) {
            return false;
        }
        if ($first === 'products' && isset($segments[1])) {
            if (!ctype_digit((string) $segments[1])) {
                return false;
            }
        }
        return true;
    }

    private function track(Request $request): void
    {
        $raw = trim($request->path(), '/');
        $path = $raw === '' ? '/' : '/' . $raw;
        $sessionId = $request->hasSession() ? $request->session()->getId() : null;
        $throttleKey = 'visit:' . ($sessionId ?? $request->ip()) . ':' . md5($path);
        if (Cache::has($throttleKey)) {
            return;
        }

        $pageType = $this->pageType($path, $request);
        $visitableId = null;
        $visitableType = null;
        if ($pageType === 'product' && preg_match('#^/products/(\d+)#', $path, $m)) {
            $visitableId = (int) $m[1];
            $visitableType = 'product';
        }

        $ip = $request->ip();
        $ipHash = $ip ? hash('sha256', $ip . config('app.key')) : null;
        $ua = $request->userAgent();
        $ua = $ua ? mb_substr($ua, 0, 512) : null;

        try {
            Visit::create([
                'path' => mb_substr($path, 0, 500),
                'page_type' => $pageType,
                'session_id' => $sessionId ? mb_substr($sessionId, 0, 128) : null,
                'ip_hash' => $ipHash,
                'user_agent' => $ua,
                'visitable_id' => $visitableId,
                'visitable_type' => $visitableType,
                'visited_at' => now(),
            ]);
            Cache::put($throttleKey, 1, now()->addMinutes(self::THROTTLE_MINUTES));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Visit track failed', ['path' => $path, 'error' => $e->getMessage()]);
        }
    }

    private function pageType(string $path, Request $request): string
    {
        if ($path === '/' || $path === '') {
            return 'home';
        }
        if (preg_match('#^/products/(\d+)#', $path)) {
            return 'product';
        }
        if (str_starts_with($path, '/products')) {
            return 'products';
        }
        if (str_starts_with($path, '/contact')) {
            return 'contact';
        }
        if (str_starts_with($path, '/terms')) {
            return 'terms';
        }
        if (str_starts_with($path, '/cart')) {
            return 'cart';
        }
        if (str_starts_with($path, '/checkout')) {
            return 'checkout';
        }
        return 'other';
    }
}
