<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\CurrencyController;
use App\Http\Controllers\Admin\QrCodeController as AdminQrCodeController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\ProductReviewController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Client\CartController;
use App\Http\Controllers\QrRedirectController;
use App\Http\Controllers\Client\CatalogController;
use App\Http\Controllers\Client\CheckoutController;
use App\Http\Controllers\Client\ReviewController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ========== ROUTES PUBLIQUES ==========
Route::get('/', function () {
        return redirect()->route('client.catalog');
    })->name('home');



// ==================== ROUTES CLIENT (FRONT OFFICE) ====================
Route::prefix('/')->name('client.')->group(function () {
    
    // ==================== CATALOGUE ====================
    Route::get('/', [CatalogController::class, 'index'])->name('home');
    Route::get('catalogue', [CatalogController::class, 'index'])->name('catalog');
    Route::get('categorie/{slug}', [CatalogController::class, 'category'])->name('category');
    Route::get('produit/{slug}', [CatalogController::class, 'product'])->name('product');
    Route::get('recherche', [CatalogController::class, 'search'])->name('search');
    
    // ==================== AVIS PRODUITS ====================
    Route::prefix('reviews')->name('review.')->group(function () {
        Route::post('product/{product}', [ReviewController::class, 'store'])->name('store');
        Route::delete('{review}', [ReviewController::class, 'destroy'])->name('destroy');
        Route::get('product/{product}/list', [ReviewController::class, 'list'])->name('list');
    });
    
    // ==================== DEVISE ====================
    Route::get('change-currency/{code}', function ($code) {
        session(['currency' => strtoupper($code)]);
        return redirect()->back();
    })->name('currency');
    
    // ==================== PANIER ====================
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::post('/add', [CartController::class, 'add'])->name('add');
        Route::post('/update', [CartController::class, 'update'])->name('update');
        Route::post('/remove', [CartController::class, 'remove'])->name('remove');
        Route::post('/clear', [CartController::class, 'clear'])->name('clear');
        Route::get('/content', [CartController::class, 'content'])->name('content');
        Route::get('/mini', [CartController::class, 'miniCart'])->name('mini');
        Route::get('/validate', [CartController::class, 'validateCart'])->name('validate');
    });
    
    // ==================== CHECKOUT ====================
    Route::prefix('checkout')->name('checkout.')->group(function () {
        Route::get('/', [CheckoutController::class, 'index'])->name('index');
        Route::post('/', [CheckoutController::class, 'store'])->name('store');
        Route::get('confirmation/{order}', [CheckoutController::class, 'confirmation'])->name('confirmation');
    });
    
});

// ========== ROUTES QR CODE (REDIRECTION) ==========
Route::prefix('qr')->name('qr.')->group(function () {
    Route::get('{code}', [QrRedirectController::class, 'redirect'])->name('redirect');
    Route::get('{code}/image/{format?}', [QrRedirectController::class, 'generateImage'])->name('generate');
});

// ========== ROUTES D'AUTHENTIFICATION (Laravel Breeze) ==========
require __DIR__.'/auth.php';

// ========== ROUTES ADMIN (PROTÉGÉES) ==========
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Categories
    Route::resource('categories', CategoryController::class);
    Route::patch('categories/{category}/toggle', [CategoryController::class, 'toggleActive'])->name('categories.toggle');
    Route::post('categories/reorder', [CategoryController::class, 'reorder'])->name('categories.reorder');
    Route::get('categories/export', [CategoryController::class, 'export'])->name('categories.export');
    
    // Products
    Route::resource('products', ProductController::class);
    Route::patch('products/{product}/toggle-active', [ProductController::class, 'toggleActive'])->name('products.toggle-active');
    Route::patch('products/{product}/toggle-featured', [ProductController::class, 'toggleFeatured'])->name('products.toggle-featured');
    Route::post('products/bulk-stock', [ProductController::class, 'bulkUpdateStock'])->name('products.bulk-stock');
    Route::get('products/export', [ProductController::class, 'export'])->name('products.export');
    Route::post('products/{product}/duplicate', [ProductController::class, 'duplicate'])->name('products.duplicate');
    Route::get('products/low-stock', [ProductController::class, 'lowStock'])->name('products.low-stock');
    
    // Announcements
Route::prefix('announcements')->name('announcements.')->group(function () {
    Route::get('/', [AnnouncementController::class, 'index'])->name('index');
    Route::get('/create', [AnnouncementController::class, 'create'])->name('create');
    Route::post('/', [AnnouncementController::class, 'store'])->name('store');
    Route::get('/{announcement}/edit', [AnnouncementController::class, 'edit'])->name('edit');
    Route::put('/{announcement}', [AnnouncementController::class, 'update'])->name('update');
    Route::delete('/{announcement}', [AnnouncementController::class, 'destroy'])->name('destroy');
    Route::post('/{announcement}/toggle', [AnnouncementController::class, 'toggle'])->name('toggle');
    Route::post('/{announcement}/duplicate', [AnnouncementController::class, 'duplicate'])->name('duplicate');
    Route::post('/reorder', [AnnouncementController::class, 'reorder'])->name('reorder');
    Route::get('/export', [AnnouncementController::class, 'export'])->name('export');
    Route::get('/stats', [AnnouncementController::class, 'stats'])->name('stats');
    Route::get('/{announcement}', [AnnouncementController::class, 'show'])->name('show'); // ← COMMENTEZ OU SUPPRIMEZ CETTE LIGNE
});

    // Customers
    Route::resource('customers', CustomerController::class);
    Route::patch('customers/{customer}/toggle', [CustomerController::class, 'toggleActive'])->name('customers.toggle');
    Route::get('customers/export', [CustomerController::class, 'export'])->name('customers.export');
    Route::post('customers/{customer}/whatsapp', [CustomerController::class, 'sendWhatsapp'])->name('customers.whatsapp');
    Route::get('customers/broadcast-form', [CustomerController::class, 'broadcastForm'])->name('customers.broadcast-form');
    Route::post('customers/broadcast', [CustomerController::class, 'broadcast'])->name('customers.broadcast');
    Route::get('customers/search', [CustomerController::class, 'search'])->name('customers.search');
    Route::post('customers/refresh-stats', [CustomerController::class, 'refreshStats'])->name('customers.refresh-stats');
    Route::get('customers/inactive', [CustomerController::class, 'inactive'])->name('customers.inactive');
    Route::get('customers/top', [CustomerController::class, 'topCustomers'])->name('customers.top');
    
    // Orders
    Route::resource('orders', OrderController::class);
    Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
    Route::patch('orders/{order}/payment', [OrderController::class, 'updatePayment'])->name('orders.payment');
    Route::patch('orders/{order}/delivery-fee', [OrderController::class, 'updateDeliveryFee'])->name('orders.delivery-fee');
    Route::patch('orders/{order}/notes', [OrderController::class, 'updateNotes'])->name('orders.notes');
    Route::post('orders/{order}/items', [OrderController::class, 'addItem'])->name('orders.items.add');
    Route::put('orders/{order}/items/{itemId}', [OrderController::class, 'updateItem'])->name('orders.items.update');
    Route::delete('orders/{order}/items/{itemId}', [OrderController::class, 'removeItem'])->name('orders.items.remove');
   Route::match(['GET', 'POST'], 'orders/{order}/whatsapp-merchant', [OrderController::class, 'sendWhatsappMerchant'])->name('orders.whatsapp-merchant');
    Route::post('orders/{order}/whatsapp-customer', [OrderController::class, 'sendWhatsappCustomer'])->name('orders.whatsapp-customer');
    Route::post('orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('orders/{order}/duplicate', [OrderController::class, 'duplicate'])->name('orders.duplicate');
    Route::get('orders/export', [OrderController::class, 'export'])->name('orders.export');
    Route::get('orders/stats', [OrderController::class, 'stats'])->name('orders.stats');
    Route::get('orders/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
    Route::get('orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('orders', [OrderController::class, 'store'])->name('orders.store');
    
   // Currencies - Gestion des devises
Route::prefix('currencies')->name('currencies.')->group(function () {
    
    // Routes principales (CRUD sans show)
    Route::get('/', [CurrencyController::class, 'index'])->name('index');
    Route::get('/create', [CurrencyController::class, 'create'])->name('create');
    Route::post('/', [CurrencyController::class, 'store'])->name('store');
    Route::get('/{currency}/edit', [CurrencyController::class, 'edit'])->name('edit');
    Route::put('/{currency}', [CurrencyController::class, 'update'])->name('update');
    Route::delete('/{currency}', [CurrencyController::class, 'destroy'])->name('destroy');
    
    // Routes d'action
    Route::post('/{currency}/set-default', [CurrencyController::class, 'setDefault'])->name('set-default');
    Route::post('/{currency}/toggle-active', [CurrencyController::class, 'toggleActive'])->name('toggle-active');
    Route::post('/{currency}/update-rate', [CurrencyController::class, 'updateRate'])->name('update-rate');
    
    // Routes groupées et utilitaires
    Route::post('/bulk-update-rates', [CurrencyController::class, 'bulkUpdateRates'])->name('bulk-update-rates');
    Route::post('/sync-rates', [CurrencyController::class, 'syncRates'])->name('sync-rates');
    Route::get('/validate-rates', [CurrencyController::class, 'validateRates'])->name('validate-rates');
    
    // Routes d'export et prévisualisation
    Route::get('/export', [CurrencyController::class, 'export'])->name('export');
    Route::get('/preview', [CurrencyController::class, 'preview'])->name('preview');
    
    // Route API pour obtenir le taux (AJAX)
    Route::get('/rate/{code}', [CurrencyController::class, 'getRate'])->name('get-rate');
    Route::get('/active', [CurrencyController::class, 'getActiveCurrencies'])->name('get-active');
});
    // QR Codes
    // Dans routes/web.php, dans le groupe admin
    Route::prefix('qr-codes')->name('qr-codes.')->group(function () {
        Route::get('/', [AdminQrCodeController::class, 'index'])->name('index');
        Route::get('/create', [AdminQrCodeController::class, 'create'])->name('create');
        Route::post('/', [AdminQrCodeController::class, 'store'])->name('store');
        Route::get('{qrCode}', [AdminQrCodeController::class, 'show'])->name('show');
        Route::get('{qrCode}/edit', [AdminQrCodeController::class, 'edit'])->name('edit');
        Route::put('{qrCode}', [AdminQrCodeController::class, 'update'])->name('update');
        Route::delete('{qrCode}', [AdminQrCodeController::class, 'destroy'])->name('destroy');
        Route::post('{qrCode}/toggle-active', [AdminQrCodeController::class, 'toggleActive'])->name('toggle-active');
        Route::get('{qrCode}/download', [AdminQrCodeController::class, 'download'])->name('download');
        Route::get('{qrCode}/download-qr', [AdminQrCodeController::class, 'downloadQrOnly'])->name('download-qr');
        Route::get('{qrCode}/preview', [AdminQrCodeController::class, 'preview'])->name('preview');
    });
    // Reviews (Avis produits)
    Route::prefix('reviews')->name('reviews.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\ProductReviewController::class, 'index'])->name('index');
        Route::get('{review}', [App\Http\Controllers\Admin\ProductReviewController::class, 'show'])->name('show');
        Route::post('{review}/approve', [App\Http\Controllers\Admin\ProductReviewController::class, 'approve'])->name('approve');
        Route::post('{review}/reject', [App\Http\Controllers\Admin\ProductReviewController::class, 'reject'])->name('reject');
        Route::delete('{review}', [App\Http\Controllers\Admin\ProductReviewController::class, 'destroy'])->name('destroy');
        
        // Routes supplémentaires
        Route::post('bulk-approve', [App\Http\Controllers\Admin\ProductReviewController::class, 'bulkApprove'])->name('bulk-approve');
        Route::post('bulk-reject', [App\Http\Controllers\Admin\ProductReviewController::class, 'bulkReject'])->name('bulk-reject');
        Route::post('bulk-delete', [App\Http\Controllers\Admin\ProductReviewController::class, 'bulkDelete'])->name('bulk-delete');
        Route::get('export/csv', [App\Http\Controllers\Admin\ProductReviewController::class, 'export'])->name('export');
        Route::get('stats', [App\Http\Controllers\Admin\ProductReviewController::class, 'stats'])->name('stats');
    });
    
    // Company (informations de l'entreprise)
    Route::get('company', [CompanyController::class, 'index'])->name('company.index');
    Route::post('company', [CompanyController::class, 'update'])->name('company.update');
    
    // System maintenance
    Route::post('maintenance/toggle', function () {
        if (app()->isDownForMaintenance()) {
            //Illuminate\Support\Facades\Artisan::call('up');
            $message = 'Site réactivé.';
        } else {
            //Illuminate\Support\Facades\Artisan::call('down');
            $message = 'Mode maintenance activé.';
        }
        return redirect()->back()->with('success', $message);
    })->name('maintenance.toggle');
    
    Route::post('cache/clear', function () {
        Illuminate\Support\Facades\Artisan::call('cache:clear');
        Illuminate\Support\Facades\Artisan::call('view:clear');
        Illuminate\Support\Facades\Artisan::call('config:clear');
        Illuminate\Support\Facades\Artisan::call('route:clear');
        return redirect()->back()->with('success', 'Cache vidé avec succès.');
    })->name('cache.clear');
});
// ========== REDIRECTION APRÈS LOGIN ==========
Route::get('/redirect', function () {
    if (Auth::check() && Auth::user()->is_admin) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('client.catalog');
})->name('redirect');

// ========== FALLBACK POUR ROUTES NON TROUVÉES ==========
