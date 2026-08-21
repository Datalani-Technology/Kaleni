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

// Public routes
Route::get('/', [ProductController::class, 'home'])->name('home');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store')->middleware('throttle:3,60');
Route::get('/terms', [TermsController::class, 'index'])->name('terms');
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
});

// Checkout routes (throttle process to limit abuse)
Route::prefix('checkout')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/', [CheckoutController::class, 'process'])->name('checkout.process')->middleware('throttle:10,1');
    Route::get('/success/{orderNumber}', [CheckoutController::class, 'success'])->name('checkout.success');
});

// Payment routes
Route::prefix('payment')->group(function () {
    Route::post('/dpo', [PaymentController::class, 'initiateDPO'])->name('payment.dpo');
    Route::get('/dpo/{order}', [PaymentController::class, 'initiateDPO'])->name('payment.dpo.init');
    Route::get('/dpo/callback', [PaymentController::class, 'dpoCallback'])->name('payment.dpo.callback');
    Route::post('/whatsapp', [PaymentController::class, 'whatsappPayment'])->name('payment.whatsapp');
    Route::get('/whatsapp/{order}', [PaymentController::class, 'whatsappPayment'])->name('payment.whatsapp.init');
});

// Admin routes
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

    Route::get('/forgot-password', [AdminAuthController::class, 'showForgotPassword'])->name('admin.forgot-password');
    Route::post('/forgot-password', [AdminAuthController::class, 'sendResetLink'])->name('admin.forgot-password.send')->middleware('throttle:3,10');
    Route::get('/reset-password', [AdminAuthController::class, 'showResetPassword'])->name('admin.reset-password');
    Route::post('/reset-password', [AdminAuthController::class, 'resetPassword'])->name('admin.reset-password.store');
    
    Route::middleware(['auth:web', 'admin'])->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');

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
        });

        Route::get('/stock', [AdminStockController::class, 'index'])->name('admin.stock.index');
        Route::post('/stock/adjust', [AdminStockController::class, 'adjust'])->name('admin.stock.adjust');

        Route::get('/orders', [AdminOrderController::class, 'index'])->name('admin.orders.index');
        Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('admin.orders.show');
        Route::post('/orders/{order}', [AdminOrderController::class, 'update'])->name('admin.orders.update');

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
        
        Route::post('/products/bulk-destroy', [AdminProductController::class, 'bulkDestroy'])->name('admin.products.bulk-destroy');
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
