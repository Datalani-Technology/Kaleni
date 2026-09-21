<?php

namespace App\Providers;

use App\Models\Booking;
use App\Models\Contact;
use App\Models\MenuItem;
use App\Models\SpecialRequest;
use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
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

        // Shared brand photo set for the home hero carousel and the footer
        // background, so both always show the same rotating images.
        View::share('brandCarouselImages', [
            'images/kaleni/menu/braai-platter.png',
            'images/kaleni/menu/grilled-chicken-boerewors-pack.png',
            'images/kaleni/menu/fried-fish-plate.png',
            'images/kaleni/gallery/braai-platter-spread.png',
        ]);

        View::composer('admin.layout', function ($view) {
            $threshold = (int) config('inventory.low_stock_threshold', 5);

            $pendingBookingsQuery = Booking::where('booking_status', 'pending');
            $lowStockQuery = MenuItem::where('is_active', true)->where('stock', '<=', $threshold);
            $unreadContactsQuery = Contact::where('is_read', false);
            $newSpecialRequestsQuery = SpecialRequest::where('status', 'new');

            $pendingBookingCount = (clone $pendingBookingsQuery)->count();
            $lowStockCount = (clone $lowStockQuery)->count();
            $unreadContactCount = (clone $unreadContactsQuery)->count();
            $newSpecialRequestCount = (clone $newSpecialRequestsQuery)->count();

            $view->with('adminNotifications', [
                'total' => $pendingBookingCount + $lowStockCount + $unreadContactCount + $newSpecialRequestCount,
                'pending_order_count' => $pendingBookingCount,
                'low_stock_count' => $lowStockCount,
                'unread_contact_count' => $unreadContactCount,
                'new_special_request_count' => $newSpecialRequestCount,
                'orders' => $pendingBookingsQuery->latest()->limit(5)->get(['id', 'booking_number', 'customer_name', 'total_amount', 'created_at']),
                'products' => $lowStockQuery->orderBy('stock')->limit(5)->get(['id', 'name', 'stock', 'updated_at']),
                'contacts' => $unreadContactsQuery->latest()->limit(5)->get(['id', 'name', 'subject', 'created_at']),
                'special_requests' => $newSpecialRequestsQuery->latest()->limit(5)->get(['id', 'name', 'occasion', 'event_date', 'created_at']),
            ]);
        });

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
