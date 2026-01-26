<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Store\Auth\LoginController;
use App\Http\Controllers\Store\Auth\ForgotPasswordController;
use App\Http\Controllers\Store\Auth\ResetPasswordController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Store\StoreRoleController;
use App\Http\Controllers\Store\StorePermissionController;
use App\Http\Controllers\Store\GeneralSettingController;
use App\Http\Controllers\Store\ProductCategoryController;
use App\Http\Controllers\Store\ProductController;
use App\Http\Controllers\Store\StoreCustomerController;
use App\Http\Controllers\Store\StoreAnalyticsController;
use App\Http\Controllers\Store\ProductSubcategoryController;
use App\Http\Controllers\StoreProfileController;
use App\Http\Controllers\Store\StoreInventoryController;
use App\Http\Controllers\Store\StaffController;
use App\Http\Controllers\Store\StoreProductController;
use App\Http\Controllers\Store\StoreRecallController;
use App\Http\Controllers\Store\StoreStockControlController;
use App\Http\Controllers\Warehouse\ProductController as WarehouseProductController;

Route::middleware('guest')->group(function () {
    Route::get('/', fn() => redirect()->route('login'));
    Route::get('/login', [LoginController::class, 'showLoginForm'])
        ->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])
        ->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])
        ->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])
        ->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])
        ->name('password.update');
});
Route::get('/pos-test', function () {
    return view('pos-test');
});
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])
        ->name('logout');
    Route::get('/dashboard', fn() => view('dashboard'))
        ->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])
        ->name('profile.password');
    Route::get('/my-store', [StoreProfileController::class, 'index'])
        ->name('store.index');
    Route::get('/store/edit/{store}', [StoreProfileController::class, 'edit'])
        ->name('store.edit');
    Route::resource('customers', StoreCustomerController::class);
    Route::put('/store/update/{store}', [StoreProfileController::class, 'update'])
        ->name('store.update');
    Route::post('/store/update-status', [StoreProfileController::class, 'updateStatus'])
        ->name('store.update-status');
    Route::resource('roles', StoreRoleController::class);
    Route::resource('permissions', StorePermissionController::class);
    Route::resource('staff', StaffController::class);
    Route::post('/staff/update-status', [StaffController::class, 'updateStatus'])->name('staff.update-status');
    Route::get('/settings/general', [GeneralSettingController::class, 'index'])->name('settings.general');
    Route::put('/settings/general', [GeneralSettingController::class, 'update'])->name('settings.update');
    Route::get('/stocks', [StoreInventoryController::class, 'index'])->name('inventory.index');
    Route::post('/inventory/request', [StoreInventoryController::class, 'requestStock'])->name('inventory.request');
    Route::get('/stocks/requests', [StoreInventoryController::class, 'requests'])->name('inventory.requests');
    Route::get('/inventory/requests/{id}', [StoreInventoryController::class, 'showRequest'])->name('inventory.requests.show');
    Route::get('/inventory/history/{id}', [StoreInventoryController::class, 'history'])->name('inventory.history');
    Route::post('/inventory/requests/upload-proof', [StoreInventoryController::class, 'uploadPaymentProof'])->name('inventory.requests.upload_proof');
    Route::delete('/inventory/requests/{id}', [StoreInventoryController::class, 'cancelRequest'])->name('inventory.requests.destroy');
    Route::get('/stocks/requests/sample', [StoreInventoryController::class, 'downloadSampleCsv'])->name('inventory.requests.sample');
    Route::post('/stocks/requests/import', [StoreInventoryController::class, 'importStockRequests'])->name('inventory.requests.import');
    Route::get('/stocks/adjustments', [StoreInventoryController::class, 'adjustments'])->name('inventory.adjustments');
    Route::post('/stocks/adjustments', [StoreInventoryController::class, 'storeAdjustment'])->name('inventory.adjustments.store');
    Route::get('/inventory/requests/{id}/challan', [StoreInventoryController::class, 'downloadChallan'])
        ->name('inventory.requests.challan');
    // STOCK CONTROL MODULE - All Routes
    Route::prefix('store/stock-control')->name('store.stock-control.')->group(function () {
        // Overview
        Route::get('/overview', [StoreStockControlController::class, 'overview'])->name('overview');
        Route::get('/overview/data', [StoreStockControlController::class, 'overviewData'])->name('overview.data');

        // Request Stock
        Route::get('/requests', [StoreStockControlController::class, 'requests'])->name('requests');
        Route::post('/requests', [StoreStockControlController::class, 'storeRequest'])->name('requests.store');

        // Pending Received
        Route::get('/received', [StoreStockControlController::class, 'received'])->name('received');
        Route::post('/received/{id}/confirm', [StoreStockControlController::class, 'confirmReceived'])->name('received.confirm');

        // Low Stock & Reorder
        Route::get('/low-stock', [StoreStockControlController::class, 'lowStock'])->name('low-stock');
        Route::get('/low-stock/data', [StoreStockControlController::class, 'lowStockData'])->name('low-stock.data');
        Route::post('/low-stock/request', [StoreStockControlController::class, 'quickRequest'])->name('low-stock.request');

        // Valuation
        Route::get('/valuation', [StoreStockControlController::class, 'valuation'])->name('valuation');

        // Expiry & Damage Alert
        Route::get('/expiry', [StoreStockControlController::class, 'expiry'])->name('expiry');
        Route::get('/expiry/data', [StoreStockControlController::class, 'expiryData'])->name('expiry.data');

        // Recall Requests (Store View)
        Route::post('/recall/{recall}/approve', [StoreRecallController::class, 'approve'])->name('recall.approve');
        Route::post('/recall/{recall}/reject', [StoreRecallController::class, 'reject'])->name('recall.reject');
        // Stock Control & Recall
        Route::get('/recall', [StoreRecallController::class, 'index'])->name('recall.index');
        Route::get('/recall/create', [StoreRecallController::class, 'create'])->name('recall.create');
        Route::post('/recall', [StoreRecallController::class, 'store'])->name('recall.store');
        Route::get('/recall/{recall}', [StoreRecallController::class, 'show'])->name('recall.show');
        Route::post('/recall/{recall}/dispatch', [StoreRecallController::class, 'dispatch'])->name('recall.dispatch');
        Route::get('/recall/{recall}/challan', [StoreRecallController::class, 'downloadChallan'])->name('recall.challan');
    });
    Route::prefix('store')->name('store.')->group(function () {
        Route::get('products/{id}/analytics', [StoreProductController::class, 'analytics'])->name('products.analytics');
        Route::get('categories/{id}/analytics', [ProductCategoryController::class, 'analytics'])->name('categories.analytics');
        Route::get('subcategories/{id}/analytics', [ProductSubcategoryController::class, 'analytics'])->name('subcategories.analytics');
        Route::resource('categories', ProductCategoryController::class)->except('show');
        Route::post('categories/import', [ProductCategoryController::class, 'import'])->name('categories.import');
        Route::get('categories/export', [ProductCategoryController::class, 'export'])->name('categories.export');
        Route::post('categories/status', [ProductCategoryController::class, 'updateStatus'])->name('categories.status');
        Route::resource('subcategories', ProductSubcategoryController::class);
        Route::post('subcategories/import', [ProductSubcategoryController::class, 'import'])->name('subcategories.import');
        Route::get('subcategories/export', [ProductSubcategoryController::class, 'export'])->name('subcategories.export');
        Route::post('subcategories/get-by-category', [ProductSubcategoryController::class, 'getByCategory'])->name('subcategories.get');
        Route::get('/analytics', [StoreAnalyticsController::class, 'index'])->name('analytics.index');
        Route::resource('products', StoreProductController::class);
        Route::post('products/import', [StoreProductController::class, 'import'])->name('products.import');
        Route::get('products/export', [StoreProductController::class, 'export'])->name('products.export');
        Route::post('products/status', [StoreProductController::class, 'updateStatus'])->name('products.status');
    });
});
