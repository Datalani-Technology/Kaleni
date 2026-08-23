<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTwoFactorEnabled
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user && !$user->hasEnabledTwoFactor()) {
            return redirect()->route('admin.2fa.setup')
                ->with('warning', 'Set up two-factor authentication to continue.');
        }

        return $next($request);
    }
}
