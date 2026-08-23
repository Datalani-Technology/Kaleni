<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\TermsController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\LogoController as AdminLogoController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\StockController as AdminStockController;
use App\Http\Controllers\Admin\PromotionCatalogController as AdminPromotionCatalogController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ContactController as AdminContactController;
use App\Http\Controllers\Admin\GalleryController as AdminGalleryController;
use App\Http\Controllers\Admin\TwoFactorController as AdminTwoFactorController;
use App\Http\Controllers\Admin\TwoFactorChallengeController as AdminTwoFactorChallengeController;
use App\Http\Controllers\Admin\AuditLogController as AdminAuditLogController;
use App\Http\Controllers\Admin\ExpenseController as AdminExpenseController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;

// Public routes
Route::get('/', [ProductController::class, 'home'])->name('home');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store')->middleware('throttle:3,60');
Route::get('/terms', [TermsController::class, 'index'])->name('terms');
Route::get('/delivery', [TermsController::class, 'delivery'])->name('delivery');
Route::get('/returns', [TermsController::class, 'returns'])->name('returns');
Route::get('/privacy', [\App\Http\Controllers\PrivacyController::class, 'index'])->name('privacy');
Route::get('/promotion', [PromotionController::class, 'index'])->name('promotion');
Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery');

// SEO Routes
Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [\App\Http\Controllers\RobotsController::class, 'index'])->name('robots');

// Cart routes
Route::prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('cart.index');
    Route::post('/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/update/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/clear', [CartController::class, 'clear'])->name('cart.clear');
    Route::post('/apply-promo', [CartController::class, 'applyPromo'])->name('cart.apply-promo')->middleware('throttle:15,1');
    Route::post('/remove-promo', [CartController::class, 'removePromo'])->name('cart.remove-promo');
});

// Checkout routes (throttle process to limit abuse)
Route::prefix('checkout')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/', [CheckoutController::class, 'process'])->name('checkout.process')->middleware('throttle:10,1');
    Route::get('/success/{orderNumber}', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/receipt/{orderNumber}', [CheckoutController::class, 'receipt'])->name('checkout.receipt');
});

// Payment routes
Route::prefix('payment')->group(function () {
    Route::post('/dpo', [PaymentController::class, 'initiateDPO'])->name('payment.dpo')->middleware('throttle:20,1');
    Route::get('/dpo/{order}', [PaymentController::class, 'initiateDPO'])->name('payment.dpo.init')->middleware('throttle:20,1');
    Route::get('/dpo/callback', [PaymentController::class, 'dpoCallback'])->name('payment.dpo.callback');
    // Server-to-server DPO Payment Notification (PNURL) — DPO posts here directly, not via the customer's browser.
    Route::post('/dpo/notify', [PaymentController::class, 'dpoNotify'])->name('payment.dpo.notify');
    Route::post('/whatsapp', [PaymentController::class, 'whatsappPayment'])->name('payment.whatsapp')->middleware('throttle:20,1');
    Route::get('/whatsapp/{order}', [PaymentController::class, 'whatsappPayment'])->name('payment.whatsapp.init')->middleware('throttle:20,1');
});

// Admin routes — served under a private, unguessable path (see config/admin.php).
// Never reference '/admin' literally elsewhere; use the admin.* route names or
// config('admin.path'). This whole group is also marked noindex/nofollow.
Route::prefix(config('admin.path'))->middleware('noindex')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

    Route::get('/forgot-password', [AdminAuthController::class, 'showForgotPassword'])->name('admin.forgot-password');
    Route::post('/forgot-password', [AdminAuthController::class, 'sendResetLink'])->name('admin.forgot-password.send')->middleware('throttle:3,10');
    Route::get('/reset-password', [AdminAuthController::class, 'showResetPassword'])->name('admin.reset-password');
    Route::post('/reset-password', [AdminAuthController::class, 'resetPassword'])->name('admin.reset-password.store')->middleware('throttle:10,10');

    // Second login factor: password already verified, session not yet fully authenticated.
    Route::get('/login/verify', [AdminTwoFactorChallengeController::class, 'show'])->name('admin.2fa.challenge');
    Route::post('/login/verify', [AdminTwoFactorChallengeController::class, 'verify'])->name('admin.2fa.challenge.verify')->middleware('throttle:10,10');

    Route::middleware(['auth:web', 'admin', 'admin.session'])->group(function () {
        // Reachable pre-2FA so a user without 2FA yet configured can actually set it up.
        Route::get('/2fa/setup', [AdminTwoFactorController::class, 'showSetup'])->name('admin.2fa.setup');
        Route::post('/2fa/enable', [AdminTwoFactorController::class, 'enable'])->name('admin.2fa.enable');
        Route::get('/2fa/recovery-codes', [AdminTwoFactorController::class, 'showRecoveryCodes'])->name('admin.2fa.recovery-codes');

        Route::middleware('two_factor')->group(function () {
            Route::get('/dashboard', function () {
                return view('admin.dashboard');
            })->name('admin.dashboard');

            Route::get('/2fa', [AdminTwoFactorController::class, 'manage'])->name('admin.2fa.manage');
            Route::post('/2fa/regenerate-codes', [AdminTwoFactorController::class, 'regenerateCodes'])->name('admin.2fa.regenerate-codes');
            Route::post('/2fa/reset', [AdminTwoFactorController::class, 'reset'])->name('admin.2fa.reset');

            Route::middleware('can_manage_users')->group(function () {
                Route::get('/logo', [AdminLogoController::class, 'edit'])->name('admin.logo.edit');
                Route::post('/logo', [AdminLogoController::class, 'update'])->name('admin.logo.update');
                Route::get('/promotion', [AdminPromotionCatalogController::class, 'edit'])->name('admin.promotion.edit');
                Route::post('/promotion', [AdminPromotionCatalogController::class, 'update'])->name('admin.promotion.update');
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

            Route::get('/orders/export/csv', [AdminOrderController::class, 'export'])->name('admin.orders.export');
            Route::get('/orders', [AdminOrderController::class, 'index'])->name('admin.orders.index');
            Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('admin.orders.show');
            Route::post('/orders/{order}', [AdminOrderController::class, 'update'])->name('admin.orders.update');
            Route::get('/orders/{order}/invoice', [AdminOrderController::class, 'invoice'])->name('admin.orders.invoice');

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

            Route::resource('promo-codes', \App\Http\Controllers\Admin\PromoCodeController::class)->names([
                'index' => 'admin.promo-codes.index',
                'create' => 'admin.promo-codes.create',
                'store' => 'admin.promo-codes.store',
                'edit' => 'admin.promo-codes.edit',
                'update' => 'admin.promo-codes.update',
                'destroy' => 'admin.promo-codes.destroy',
            ])->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

            Route::post('/products/bulk-destroy', [AdminProductController::class, 'bulkDestroy'])->name('admin.products.bulk-destroy');
            Route::get('/products/export/csv', [AdminProductController::class, 'export'])->name('admin.products.export');
            Route::resource('products', AdminProductController::class)->names([
                'index' => 'admin.products.index',
                'create' => 'admin.products.create',
                'store' => 'admin.products.store',
                'show' => 'admin.products.show',
                'edit' => 'admin.products.edit',
                'update' => 'admin.products.update',
                'destroy' => 'admin.products.destroy',
            ]);
        });
    });
});
