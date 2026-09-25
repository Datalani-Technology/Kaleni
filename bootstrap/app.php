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

        // 419 Page Expired on admin login / forgot / reset → redirect with friendly message.
        // Laravel's handler internally rewrites TokenMismatchException into a plain
        // HttpException(419, ...) (see Handler::prepareException()) BEFORE checking
        // custom render callbacks, so a callback typed to TokenMismatchException
        // itself never matches — it has to catch the resulting HttpException and
        // check the status code (the original exception survives as getPrevious()).
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, \Illuminate\Http\Request $request) {
            if ($e->getStatusCode() !== 419) {
                return null;
            }
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

        // A throttled form submission (contact, special request/quote,
        // booking, order, etc.) otherwise falls through to Laravel's raw
        // exception page instead of the site's own pop-up — send the visitor
        // back to what they were doing with a friendly, actionable message.
        // Fetch-based submissions (booking/order checkout send
        // Accept: application/json) are left alone: their own JS already
        // falls back to a normal form submission on any non-ok response,
        // which then hits this same handler the second time around.
        $exceptions->render(function (\Illuminate\Http\Exceptions\ThrottleRequestsException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return null;
            }

            $retryAfter = $e->getHeaders()['Retry-After'] ?? null;
            $message = $retryAfter
                ? "You've submitted this too many times. Please wait " . max(1, ceil($retryAfter / 60)) . ' minute(s) and try again.'
                : "You've submitted this too many times. Please wait a few minutes and try again.";

            // The home page's "Get a quote" form posts to the same URL as
            // the previous page (itself), so the generic previous-page
            // fallback below would strand the visitor at the top of a long
            // page instead of back at the form they were just filling in.
            $fallback = $request->is('special-requests')
                && $request->input('source') === \App\Models\SpecialRequest::SOURCE_HOME_QUOTE_FORM
                ? route('home') . '#get-a-quote'
                : (url()->previous() ?: route('home'));

            return redirect($fallback)
                ->withInput($request->except(['password', 'password_confirmation']))
                ->with('error', $message);
        });
    })->create();
