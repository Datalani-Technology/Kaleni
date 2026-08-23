<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        if (env('DEPLOY_FLAT')) {
            $this->app->instance('path.public', base_path());
        }

        // Configure authentication redirect for admin routes
        Authenticate::redirectUsing(function ($request) {
            if ($request->is(config('admin.path').'/*') || $request->routeIs('admin.*')) {
                return route('admin.login');
            }
            return route('home');
        });

        if (config('app.force_https')) {
            URL::forceScheme('https');
        }

        // Applies everywhere Password::defaults() is used (admin password
        // reset/change forms). uncompromised() checks the password against the
        // Have I Been Pwned breach corpus — only enabled in production so local
        // dev/tests don't depend on outbound internet access.
        Password::defaults(function () {
            $rule = Password::min(10)->letters()->mixedCase()->numbers()->symbols();
            return $this->app->environment('production') ? $rule->uncompromised() : $rule;
        });
    }
}
