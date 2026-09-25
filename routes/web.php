<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\TermsController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\FoodOfTheDayController;
use App\Http\Controllers\SpecialRequestController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\LogoController as AdminLogoController;
use App\Http\Controllers\Admin\MenuItemController as AdminMenuItemController;
use App\Http\Controllers\Admin\StockController as AdminStockController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\ContactController as AdminContactController;
use App\Http\Controllers\Admin\GalleryController as AdminGalleryController;
use App\Http\Controllers\Admin\AuditLogController as AdminAuditLogController;
use App\Http\Controllers\Admin\ExpenseController as AdminExpenseController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\FoodOfTheDayController as AdminFoodOfTheDayController;
use App\Http\Controllers\Admin\SpecialRequestController as AdminSpecialRequestController;

// Public routes
Route::get('/', [MenuController::class, 'home'])->name('home');
Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
Route::get('/menu/{id}', [MenuController::class, 'show'])->name('menu.show');
Route::get('/food-of-the-day', [FoodOfTheDayController::class, 'index'])->name('food-of-the-day.index');
Route::get('/special-requests', [SpecialRequestController::class, 'create'])->name('special-requests.create');
Route::post('/special-requests', [SpecialRequestController::class, 'store'])->name('special-requests.store')->middleware('throttle:5,60');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store')->middleware('throttle:3,60');
Route::get('/terms', [TermsController::class, 'index'])->name('terms');
Route::get('/delivery', [TermsController::class, 'delivery'])->name('delivery');
Route::get('/cancellations', [TermsController::class, 'cancellations'])->name('cancellations');
Route::get('/privacy', [\App\Http\Controllers\PrivacyController::class, 'index'])->name('privacy');
Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery');

// SEO Routes
Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [\App\Http\Controllers\RobotsController::class, 'index'])->name('robots');

// Cart routes ("cart" kept as a generic UI term for the in-progress order)
Route::prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('cart.index');
    Route::post('/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/update/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/clear', [CartController::class, 'clear'])->name('cart.clear');
    Route::post('/apply-promo', [CartController::class, 'applyPromo'])->name('cart.apply-promo')->middleware('throttle:15,1');
    Route::post('/remove-promo', [CartController::class, 'removePromo'])->name('cart.remove-promo');
});

// Booking routes (throttle process to limit abuse)
Route::prefix('booking')->group(function () {
    Route::get('/', [BookingController::class, 'index'])->name('booking.index');
    Route::post('/', [BookingController::class, 'store'])->name('booking.store')->middleware('throttle:10,1');
    Route::get('/success/{bookingNumber}', [BookingController::class, 'success'])->name('booking.success');
    Route::get('/receipt/{bookingNumber}', [BookingController::class, 'receipt'])->name('booking.receipt');
});

// Quick food order routes — ordering without booking a full catered event.
// success/receipt are shared with booking.* since they just look up a
// Booking by number regardless of order_type.
Route::prefix('order')->group(function () {
    Route::get('/', [OrderController::class, 'index'])->name('order.index');
    Route::post('/', [OrderController::class, 'store'])->name('order.store')->middleware('throttle:10,1');
});

// Payment routes
Route::prefix('payment')->group(function () {
    Route::post('/dpo', [PaymentController::class, 'initiateDPO'])->name('payment.dpo')->middleware('throttle:20,1');
    Route::get('/dpo/{booking}', [PaymentController::class, 'initiateDPO'])->name('payment.dpo.init')->middleware('throttle:20,1');
    Route::get('/dpo/callback', [PaymentController::class, 'dpoCallback'])->name('payment.dpo.callback');
    // Server-to-server DPO Payment Notification (PNURL) — DPO posts here directly, not via the customer's browser.
    Route::post('/dpo/notify', [PaymentController::class, 'dpoNotify'])->name('payment.dpo.notify');
    Route::post('/whatsapp', [PaymentController::class, 'whatsappPayment'])->name('payment.whatsapp')->middleware('throttle:20,1');
    Route::get('/whatsapp/{booking}', [PaymentController::class, 'whatsappPayment'])->name('payment.whatsapp.init')->middleware('throttle:20,1');
});

// Admin routes — served under a private, unguessable path (see config/admin.php).
// Never reference '/admin' literally elsewhere; use the admin.* route names or
// config('admin.path'). This whole group is also marked noindex/nofollow.
Route::prefix(config('admin.path'))->middleware('noindex')->group(function () {
    Route::get('/', function () {
        return auth('web')->check() ? redirect()->route('admin.dashboard') : redirect()->route('admin.login');
    })->name('admin.home');
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

    Route::get('/forgot-password', [AdminAuthController::class, 'showForgotPassword'])->name('admin.forgot-password');
    Route::post('/forgot-password', [AdminAuthController::class, 'sendResetLink'])->name('admin.forgot-password.send')->middleware('throttle:3,10');
    Route::get('/reset-password', [AdminAuthController::class, 'showResetPassword'])->name('admin.reset-password');
    Route::post('/reset-password', [AdminAuthController::class, 'resetPassword'])->name('admin.reset-password.store')->middleware('throttle:10,10');

    Route::middleware(['auth:web', 'admin', 'admin.session'])->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');

        Route::get('/notifications/poll', [\App\Http\Controllers\Admin\NotificationController::class, 'poll'])->name('admin.notifications.poll');

        Route::middleware('can_manage_users')->group(function () {
                Route::get('/logo', [AdminLogoController::class, 'edit'])->name('admin.logo.edit');
                Route::post('/logo', [AdminLogoController::class, 'update'])->name('admin.logo.update');
                Route::resource('users', \App\Http\Controllers\Admin\UserController::class)->names([
                    'index' => 'admin.users.index',
                    'create' => 'admin.users.create',
                    'store' => 'admin.users.store',
                    'show' => 'admin.users.show',
                    'edit' => 'admin.users.edit',
                    'update' => 'admin.users.update',
                    'destroy' => 'admin.users.destroy',
                ]);
                Route::get('/audit-log', [AdminAuditLogController::class, 'index'])->name('admin.audit-log.index');
            });

            Route::get('/stock', [AdminStockController::class, 'index'])->name('admin.stock.index');
            Route::post('/stock/adjust', [AdminStockController::class, 'adjust'])->name('admin.stock.adjust');

            Route::get('/expenses', [AdminExpenseController::class, 'index'])->name('admin.expenses.index');
            Route::get('/expenses/create', [AdminExpenseController::class, 'create'])->name('admin.expenses.create');
            Route::post('/expenses', [AdminExpenseController::class, 'store'])->name('admin.expenses.store');
            Route::delete('/expenses/{expense}', [AdminExpenseController::class, 'destroy'])->name('admin.expenses.destroy');
            Route::get('/expenses/export/csv', [AdminExpenseController::class, 'export'])->name('admin.expenses.export');

            Route::get('/reports', [AdminReportController::class, 'index'])->name('admin.reports.index');
            Route::get('/reports/document', [AdminReportController::class, 'document'])->name('admin.reports.document');
            Route::get('/customers', [AdminCustomerController::class, 'index'])->name('admin.customers.index');
            Route::get('/customers/{customer}', [AdminCustomerController::class, 'show'])->name('admin.customers.show');
            Route::post('/customers/{customer}', [AdminCustomerController::class, 'update'])->name('admin.customers.update');

            Route::get('/bookings/export/csv', [AdminBookingController::class, 'export'])->name('admin.bookings.export');
            Route::get('/bookings', [AdminBookingController::class, 'index'])->name('admin.bookings.index');
            Route::get('/bookings/{booking}', [AdminBookingController::class, 'show'])->name('admin.bookings.show');
            Route::post('/bookings/{booking}', [AdminBookingController::class, 'update'])->name('admin.bookings.update');
            Route::delete('/bookings/{booking}', [AdminBookingController::class, 'destroy'])->name('admin.bookings.destroy');
            Route::get('/bookings/{booking}/invoice', [AdminBookingController::class, 'invoice'])->name('admin.bookings.invoice');
            Route::post('/bookings/{booking}/invoice/email', [AdminBookingController::class, 'emailInvoice'])->name('admin.bookings.invoice.email');

            Route::get('/orders/export/csv', [\App\Http\Controllers\Admin\OrderController::class, 'export'])->name('admin.orders.export');
            Route::get('/orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('admin.orders.index');

            Route::get('/special-requests', [AdminSpecialRequestController::class, 'index'])->name('admin.special-requests.index');
            Route::get('/special-requests/{specialRequest}', [AdminSpecialRequestController::class, 'show'])->name('admin.special-requests.show');
            Route::post('/special-requests/{specialRequest}', [AdminSpecialRequestController::class, 'update'])->name('admin.special-requests.update');
            Route::delete('/special-requests/{specialRequest}', [AdminSpecialRequestController::class, 'destroy'])->name('admin.special-requests.destroy');
            Route::post('/special-requests/{specialRequest}/send-quote', [AdminSpecialRequestController::class, 'sendQuote'])->name('admin.special-requests.send-quote');
            Route::post('/special-requests/{specialRequest}/send-quote-whatsapp', [AdminSpecialRequestController::class, 'sendQuoteWhatsapp'])->name('admin.special-requests.send-quote-whatsapp');
            Route::get('/special-requests/{specialRequest}/quote', [AdminSpecialRequestController::class, 'quote'])->name('admin.special-requests.quote');

            Route::get('/quotes', [\App\Http\Controllers\Admin\QuoteController::class, 'index'])->name('admin.quotes.index');

            Route::resource('food-of-the-day', AdminFoodOfTheDayController::class)->names([
                'index' => 'admin.food-of-the-day.index',
                'create' => 'admin.food-of-the-day.create',
                'store' => 'admin.food-of-the-day.store',
                'edit' => 'admin.food-of-the-day.edit',
                'update' => 'admin.food-of-the-day.update',
                'destroy' => 'admin.food-of-the-day.destroy',
            ])->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

            Route::get('/contacts', [AdminContactController::class, 'index'])->name('admin.contacts.index');
            Route::get('/contacts/{contact}', [AdminContactController::class, 'show'])->name('admin.contacts.show');

            Route::resource('gallery', AdminGalleryController::class)->names([
                'index' => 'admin.gallery.index',
                'create' => 'admin.gallery.create',
                'store' => 'admin.gallery.store',
                'edit' => 'admin.gallery.edit',
                'update' => 'admin.gallery.update',
                'destroy' => 'admin.gallery.destroy',
            ])->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

            Route::get('/analytics', [\App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('admin.analytics.index');
            Route::get('/finance', [\App\Http\Controllers\Admin\FinanceController::class, 'index'])->name('admin.finance.index');

            Route::resource('promo-codes', \App\Http\Controllers\Admin\PromoCodeController::class)->names([
                'index' => 'admin.promo-codes.index',
                'create' => 'admin.promo-codes.create',
                'store' => 'admin.promo-codes.store',
                'edit' => 'admin.promo-codes.edit',
                'update' => 'admin.promo-codes.update',
                'destroy' => 'admin.promo-codes.destroy',
            ])->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

            Route::post('/menu-items/bulk-destroy', [AdminMenuItemController::class, 'bulkDestroy'])->name('admin.menu-items.bulk-destroy');
            Route::get('/menu-items/export/csv', [AdminMenuItemController::class, 'export'])->name('admin.menu-items.export');
            Route::resource('menu-items', AdminMenuItemController::class)->names([
                'index' => 'admin.menu-items.index',
                'create' => 'admin.menu-items.create',
                'store' => 'admin.menu-items.store',
                'show' => 'admin.menu-items.show',
                'edit' => 'admin.menu-items.edit',
                'update' => 'admin.menu-items.update',
                'destroy' => 'admin.menu-items.destroy',
            ]);
    });
});
