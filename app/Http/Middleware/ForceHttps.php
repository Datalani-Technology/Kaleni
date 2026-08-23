<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Opt-in HTTPS redirect, gated by config('app.force_https') /
 * env FORCE_HTTPS — off by default so local http:// development keeps working.
 * Enable once a valid SSL certificate is installed on the production domain.
 */
class ForceHttps
{
    public function handle(Request $request, Closure $next): Response
    {
        if (config('app.force_https') && !$request->secure()) {
            return redirect()->secure($request->getRequestUri(), 301);
        }

        return $next($request);
    }
}
