<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'can_manage_users' => \App\Http\Middleware\CanManageUsers::class,
            'security.headers' => \App\Http\Middleware\SecurityHeadersMiddleware::class,
            'noindex' => \App\Http\Middleware\NoIndexHeader::class,
            'admin.session' => \App\Http\Middleware\AdminSessionSecurity::class,
        ]);

        // Opt-in HTTPS redirect (config('app.force_https') / env FORCE_HTTPS)
        $middleware->prepend(\App\Http\Middleware\ForceHttps::class);
        // Apply security headers to all requests
        $middleware->append(\App\Http\Middleware\SecurityHeadersMiddleware::class);
        // Track page views for analytics (skips admin, assets, etc.)
        $middleware->web(append: [\App\Http\Middleware\VisitTrackerMiddleware::class]);

        // DPO's server-to-server payment notification (PNURL) is a POST from DPO's
        // servers, not a browser form submission — it can't carry our CSRF token.
        $middleware->validateCsrfTokens(except: [
            'payment/dpo/notify',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle authentication exceptions for admin routes
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, \Illuminate\Http\Request $request) {
            $adminPath = config('admin.path');
            if ($request->is($adminPath.'/*') || $request->routeIs('admin.*')) {
                return redirect()->route('admin.login');
            }
            return null;
        });

        // 419 Page Expired on admin login / forgot / reset → redirect with friendly message
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, \Illuminate\Http\Request $request) {
            $adminPath = config('admin.path');
            if ($request->is($adminPath.'/login') && $request->isMethod('POST')) {
                return redirect()->route('admin.login')->with('error', 'Your session expired. Please try again.');
            }
            if ($request->is($adminPath.'/forgot-password') && $request->isMethod('POST')) {
                return redirect()->route('admin.forgot-password')->with('error', 'Your session expired. Please try again.');
            }
            if ($request->is($adminPath.'/reset-password') && $request->isMethod('POST')) {
                return redirect()->route('admin.forgot-password')->with('error', 'Your session expired. Request a new reset link.');
            }
            if ($request->is($adminPath.'/login/verify') && $request->isMethod('POST')) {
                return redirect()->route('admin.login')->with('error', 'Your session expired. Please log in again.');
            }
            return null;
        });
    })->create();
