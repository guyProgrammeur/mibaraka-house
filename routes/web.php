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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Web Routes - Mibaraka House
|--------------------------------------------------------------------------
*/

// ==================== ROUTES CLIENT (FRONT OFFICE) ====================
Route::name('client.')->group(function () {
    
    // Catalogue
    Route::get('/', [CatalogController::class, 'index'])->name('home');
    Route::get('/catalogue', [CatalogController::class, 'index'])->name('catalog');
    Route::get('/categorie/{slug}', [CatalogController::class, 'category'])->name('category');
    Route::get('/produit/{slug}', [CatalogController::class, 'product'])->name('product');
    Route::get('/recherche', [CatalogController::class, 'search'])->name('search');
    
    // Avis produits
    Route::prefix('reviews')->name('review.')->group(function () {
        Route::get('/product/{product}/list', [ReviewController::class, 'list'])->name('list');
        Route::post('/product/{product}', [ReviewController::class, 'store'])->name('store');
        Route::delete('/{review}', [ReviewController::class, 'destroy'])->name('destroy');
    });
    
    // Devise
    Route::get('/change-currency/{code}', function ($code) {
        session(['currency' => strtoupper($code)]);
        return redirect()->back();
    })->name('currency')->middleware('throttle:30,1');
    
    // Panier
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::get('/content', [CartController::class, 'content'])->name('content');
        Route::get('/mini', [CartController::class, 'miniCart'])->name('mini');
        Route::get('/validate', [CartController::class, 'validateCart'])->name('validate');
        Route::post('/add', [CartController::class, 'add'])->name('add')->middleware('throttle:10,1');
        Route::post('/update', [CartController::class, 'update'])->name('update')->middleware('throttle:30,1');
        Route::post('/remove', [CartController::class, 'remove'])->name('remove')->middleware('throttle:30,1');
        Route::post('/clear', [CartController::class, 'clear'])->name('clear')->middleware('throttle:5,1');
    });
    
    // Checkout
    Route::prefix('checkout')->name('checkout.')->group(function () {
        Route::get('/', [CheckoutController::class, 'index'])->name('index');
        Route::post('/', [CheckoutController::class, 'store'])->name('store')->middleware('throttle:3,10');
        Route::get('/confirmation/{order}', [CheckoutController::class, 'confirmation'])->name('confirmation');
    });
});

// ==================== ROUTES QR CODE (REDIRECTION) ====================
Route::prefix('qr')->name('qr.')->group(function () {
    Route::get('/{code}', [QrRedirectController::class, 'redirect'])->name('redirect');
    Route::get('/{code}/image/{format?}', [QrRedirectController::class, 'generateImage'])->name('generate');
});

// ==================== ROUTES D'AUTHENTIFICATION ====================
require __DIR__.'/auth.php';

// ==================== REDIRECTION APRÈS LOGIN ====================
Route::get('/redirect', function () {
    if (Auth::check() && Auth::user()->is_admin) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('client.catalog');
})->name('redirect');

// ==================== ROUTES ADMIN (PROTÉGÉES) ====================
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Catégories
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::get('/create', [CategoryController::class, 'create'])->name('create');
        Route::get('/export', [CategoryController::class, 'export'])->name('export');
        Route::post('/', [CategoryController::class, 'store'])->name('store');
        Route::post('/reorder', [CategoryController::class, 'reorder'])->name('reorder');
        Route::get('/{category}/edit', [CategoryController::class, 'edit'])->name('edit');
        Route::put('/{category}', [CategoryController::class, 'update'])->name('update');
        Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('destroy');
        Route::patch('/{category}/toggle', [CategoryController::class, 'toggleActive'])->name('toggle');
    });
    
    // Produits
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('/create', [ProductController::class, 'create'])->name('create');
        Route::get('/export', [ProductController::class, 'export'])->name('export');
        Route::get('/low-stock', [ProductController::class, 'lowStock'])->name('low-stock');
        Route::post('/', [ProductController::class, 'store'])->name('store');
        Route::post('/bulk-stock', [ProductController::class, 'bulkUpdateStock'])->name('bulk-stock');
        Route::post('/{product}/duplicate', [ProductController::class, 'duplicate'])->name('duplicate');
        Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
        Route::put('/{product}', [ProductController::class, 'update'])->name('update');
        Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');
        Route::patch('/{product}/toggle-active', [ProductController::class, 'toggleActive'])->name('toggle-active');
        Route::patch('/{product}/toggle-featured', [ProductController::class, 'toggleFeatured'])->name('toggle-featured');
    });
    
    // Annonces
    Route::prefix('announcements')->name('announcements.')->group(function () {
        Route::get('/', [AnnouncementController::class, 'index'])->name('index');
        Route::get('/create', [AnnouncementController::class, 'create'])->name('create');
        Route::get('/export', [AnnouncementController::class, 'export'])->name('export');
        Route::get('/stats', [AnnouncementController::class, 'stats'])->name('stats');
        Route::post('/', [AnnouncementController::class, 'store'])->name('store');
        Route::post('/reorder', [AnnouncementController::class, 'reorder'])->name('reorder');
        Route::get('/{announcement}/edit', [AnnouncementController::class, 'edit'])->name('edit');
        Route::put('/{announcement}', [AnnouncementController::class, 'update'])->name('update');
        Route::delete('/{announcement}', [AnnouncementController::class, 'destroy'])->name('destroy');
        Route::post('/{announcement}/toggle', [AnnouncementController::class, 'toggle'])->name('toggle');
        Route::post('/{announcement}/duplicate', [AnnouncementController::class, 'duplicate'])->name('duplicate');
    });
    
    // Clients
    Route::prefix('customers')->name('customers.')->group(function () {
        Route::get('/', [CustomerController::class, 'index'])->name('index');
        Route::get('/create', [CustomerController::class, 'create'])->name('create');
        Route::get('/export', [CustomerController::class, 'export'])->name('export');
        Route::get('/search', [CustomerController::class, 'search'])->name('search');
        Route::get('/inactive', [CustomerController::class, 'inactive'])->name('inactive');
        Route::get('/top', [CustomerController::class, 'topCustomers'])->name('top');
        Route::get('/broadcast-form', [CustomerController::class, 'broadcastForm'])->name('broadcast-form');
        Route::post('/', [CustomerController::class, 'store'])->name('store');
        Route::post('/broadcast', [CustomerController::class, 'broadcast'])->name('broadcast');
        Route::post('/refresh-stats', [CustomerController::class, 'refreshStats'])->name('refresh-stats');
        Route::post('/{customer}/whatsapp', [CustomerController::class, 'sendWhatsapp'])->name('whatsapp');
        Route::get('/{customer}/edit', [CustomerController::class, 'edit'])->name('edit');
        Route::put('/{customer}', [CustomerController::class, 'update'])->name('update');
        Route::delete('/{customer}', [CustomerController::class, 'destroy'])->name('destroy');
        Route::patch('/{customer}/toggle', [CustomerController::class, 'toggleActive'])->name('toggle');
    });
    
    // Commandes
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/create', [OrderController::class, 'create'])->name('create');
        Route::get('/export', [OrderController::class, 'export'])->name('export');
        Route::get('/stats', [OrderController::class, 'stats'])->name('stats');
        Route::post('/', [OrderController::class, 'store'])->name('store');
        Route::get('/{order}', [OrderController::class, 'show'])->name('show');
        Route::get('/{order}/edit', [OrderController::class, 'edit'])->name('edit');
        Route::get('/{order}/invoice', [OrderController::class, 'invoice'])->name('invoice');
        Route::put('/{order}', [OrderController::class, 'update'])->name('update');
        Route::delete('/{order}', [OrderController::class, 'destroy'])->name('destroy');
        Route::patch('/{order}/status', [OrderController::class, 'updateStatus'])->name('status');
        Route::patch('/{order}/payment', [OrderController::class, 'updatePayment'])->name('payment');
        Route::patch('/{order}/delivery-fee', [OrderController::class, 'updateDeliveryFee'])->name('delivery-fee');
        Route::patch('/{order}/notes', [OrderController::class, 'updateNotes'])->name('notes');
        Route::post('/{order}/items', [OrderController::class, 'addItem'])->name('items.add');
        Route::put('/{order}/items/{itemId}', [OrderController::class, 'updateItem'])->name('items.update');
        Route::delete('/{order}/items/{itemId}', [OrderController::class, 'removeItem'])->name('items.remove');
        Route::match(['GET', 'POST'], '/{order}/whatsapp-merchant', [OrderController::class, 'sendWhatsappMerchant'])->name('whatsapp-merchant');
        Route::post('/{order}/whatsapp-customer', [OrderController::class, 'sendWhatsappCustomer'])->name('whatsapp-customer');
        Route::post('/{order}/cancel', [OrderController::class, 'cancel'])->name('cancel');
        Route::post('/{order}/duplicate', [OrderController::class, 'duplicate'])->name('duplicate');
    });
    
    // Devises
    Route::prefix('currencies')->name('currencies.')->group(function () {
        Route::get('/', [CurrencyController::class, 'index'])->name('index');
        Route::get('/create', [CurrencyController::class, 'create'])->name('create');
        Route::get('/export', [CurrencyController::class, 'export'])->name('export');
        Route::get('/preview', [CurrencyController::class, 'preview'])->name('preview');
        Route::get('/active', [CurrencyController::class, 'getActiveCurrencies'])->name('get-active');
        Route::get('/rate/{code}', [CurrencyController::class, 'getRate'])->name('get-rate');
        Route::post('/', [CurrencyController::class, 'store'])->name('store');
        Route::post('/bulk-update-rates', [CurrencyController::class, 'bulkUpdateRates'])->name('bulk-update-rates');
        Route::post('/sync-rates', [CurrencyController::class, 'syncRates'])->name('sync-rates');
        Route::get('/validate-rates', [CurrencyController::class, 'validateRates'])->name('validate-rates');
        Route::get('/{currency}/edit', [CurrencyController::class, 'edit'])->name('edit');
        Route::put('/{currency}', [CurrencyController::class, 'update'])->name('update');
        Route::delete('/{currency}', [CurrencyController::class, 'destroy'])->name('destroy');
        Route::post('/{currency}/set-default', [CurrencyController::class, 'setDefault'])->name('set-default');
        Route::post('/{currency}/toggle-active', [CurrencyController::class, 'toggleActive'])->name('toggle-active');
        Route::post('/{currency}/update-rate', [CurrencyController::class, 'updateRate'])->name('update-rate');
    });
    
    // QR Codes
    Route::prefix('qr-codes')->name('qr-codes.')->group(function () {
        Route::get('/', [AdminQrCodeController::class, 'index'])->name('index');
        Route::get('/create', [AdminQrCodeController::class, 'create'])->name('create');
        Route::get('/export', [AdminQrCodeController::class, 'export'])->name('export');
        Route::get('/stats', [AdminQrCodeController::class, 'stats'])->name('stats');
        Route::post('/', [AdminQrCodeController::class, 'store'])->name('store');
        Route::get('/{qrCode}', [AdminQrCodeController::class, 'show'])->name('show');
        Route::get('/{qrCode}/edit', [AdminQrCodeController::class, 'edit'])->name('edit');
        Route::get('/{qrCode}/download', [AdminQrCodeController::class, 'download'])->name('download');
        Route::get('/{qrCode}/download-qr', [AdminQrCodeController::class, 'downloadQrOnly'])->name('download-qr');
        Route::get('/{qrCode}/preview', [AdminQrCodeController::class, 'preview'])->name('preview');
        Route::put('/{qrCode}', [AdminQrCodeController::class, 'update'])->name('update');
        Route::delete('/{qrCode}', [AdminQrCodeController::class, 'destroy'])->name('destroy');
        Route::post('/{qrCode}/toggle-active', [AdminQrCodeController::class, 'toggleActive'])->name('toggle-active');
    });
    
    // Avis produits
    Route::prefix('reviews')->name('reviews.')->group(function () {
        Route::get('/', [ProductReviewController::class, 'index'])->name('index');
        Route::get('/export/csv', [ProductReviewController::class, 'export'])->name('export');
        Route::get('/stats', [ProductReviewController::class, 'stats'])->name('stats');
        Route::post('/bulk-approve', [ProductReviewController::class, 'bulkApprove'])->name('bulk-approve');
        Route::post('/bulk-reject', [ProductReviewController::class, 'bulkReject'])->name('bulk-reject');
        Route::post('/bulk-delete', [ProductReviewController::class, 'bulkDelete'])->name('bulk-delete');
        Route::get('/{review}', [ProductReviewController::class, 'show'])->name('show');
        Route::post('/{review}/approve', [ProductReviewController::class, 'approve'])->name('approve');
        Route::post('/{review}/reject', [ProductReviewController::class, 'reject'])->name('reject');
        Route::delete('/{review}', [ProductReviewController::class, 'destroy'])->name('destroy');
    });
    
    // Informations de l'entreprise
    Route::prefix('company')->name('company.')->group(function () {
        Route::get('/', [CompanyController::class, 'index'])->name('index');
        Route::post('/', [CompanyController::class, 'update'])->name('update');
    });
    
    // Maintenance système
    Route::post('/maintenance/toggle', function () {
        if (app()->isDownForMaintenance()) {
            Artisan::call('up');
            $message = 'Site réactivé avec succès.';
        } else {
            Artisan::call('down --retry=60 --secret="mibaraka2024"');
            $message = 'Mode maintenance activé. Seuls les administrateurs peuvent accéder.';
        }
        return redirect()->back()->with('success', $message);
    })->name('maintenance.toggle');
    
    // Vidage du cache optimisé
    Route::post('/cache/clear', function () {
        Artisan::call('optimize:clear');
        Artisan::call('optimize');
        return redirect()->back()->with('success', 'Cache optimisé et nettoyé avec succès.');
    })->name('cache.clear');
});

// ==================== FALLBACK POUR ROUTES NON TROUVÉES ====================
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});