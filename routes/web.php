<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Store\StoreRoleController;
use App\Http\Controllers\Store\StorePermissionController;
use App\Http\Controllers\Store\GeneralSettingController;
use App\Http\Controllers\Store\ProductCategoryController;
use App\Http\Controllers\Store\StoreSalesController;
use App\Http\Controllers\Store\StoreTransferController;
use App\Http\Controllers\Store\ProductController;
use App\Http\Controllers\Store\StoreCustomerController;
use App\Http\Controllers\Store\StoreAnalyticsController;
use App\Http\Controllers\Store\ProductSubcategoryController;
use App\Http\Controllers\StoreProfileController;
use App\Http\Controllers\Store\StoreInventoryController;
use App\Http\Controllers\Store\StaffController;
use App\Http\Controllers\Store\StoreAuditController;
use App\Http\Controllers\Store\StoreNotificationController;
use App\Http\Controllers\Store\StoreProductController;
use App\Http\Controllers\Store\StorePromotionController;
use App\Http\Controllers\Store\StoreRecallController;
use App\Http\Controllers\Store\StoreRecipeController;
use App\Http\Controllers\Store\StoreReturnController;
use App\Http\Controllers\Store\StoreStockControlController;
use App\Http\Controllers\Store\StoreSupportTicketController;
use App\Http\Controllers\Store\StoreOrderController;
use App\Http\Controllers\StoreDashboardController;
use App\Http\Controllers\Warehouse\ProductController as WarehouseProductController;
use App\Http\Controllers\Website\ProductController as WebsiteProductController;
use App\Http\Controllers\Website\CartController;

// Redundant guest group removed (handled by auth.php)
// Public-facing website module routes
// Redundant website group removed (all website routes are in website.php)



Route::get('/pos-test', function () {
    return view('pos-test');
});

Route::middleware('auth')->group(function () {
    Route::get('/store/dashboard', [StoreDashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');
    Route::resource('staff', StaffController::class);
    Route::post('/staff/update-status', [StaffController::class, 'updateStatus'])->name('staff.update-status');

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
    Route::get('/pos', [StoreSalesController::class, 'index'])->name('store.sales.pos');
    Route::get('/pos/search', [StoreSalesController::class, 'searchProduct'])->name('store.sales.search');
    Route::get('/pos/search-products', [StoreSalesController::class, 'searchProduct'])->name('store.sales.search');
    // Notifications
    Route::get('/notifications', [StoreNotificationController::class, 'index'])->name('store.notifications.index');
    Route::get('/notifications/read/{id}', [StoreNotificationController::class, 'markRead'])->name('store.notifications.read');
    Route::post('/notifications/read-all', [StoreNotificationController::class, 'markAllRead'])->name('store.notifications.readAll');
    Route::post('/notifications/clear', [StoreNotificationController::class, 'clearAll'])->name('store.notifications.clear');

    // 2. Transfer Routes
    Route::get('/transfers', [StoreTransferController::class, 'index'])->name('transfers.index');
    Route::post('/transfers', [StoreTransferController::class, 'store'])->name('transfers.store');
    Route::post('/transfers/{transfer}/dispatch', [StoreTransferController::class, 'dispatchTransfer'])->name('transfers.dispatch');
    Route::post('/transfers/{transfer}/receive', [StoreTransferController::class, 'receiveTransfer'])->name('transfers.receive');
    Route::post('/store/update-status', [StoreProfileController::class, 'updateStatus'])
        ->name('store.update-status');
    Route::resource('roles', StoreRoleController::class);
    Route::resource('permissions', StorePermissionController::class);
    Route::get('/settings/general', [GeneralSettingController::class, 'index'])->name('settings.general');
    Route::put('/settings/update', [GeneralSettingController::class, 'update'])->name('settings.update');
    Route::get('/settings/home-page', [\App\Http\Controllers\Store\HomePageSettingController::class, 'edit'])->name('settings.home-page');
    Route::put('/settings/home-page', [\App\Http\Controllers\Store\HomePageSettingController::class, 'update'])->name('settings.home-page.update');

    Route::get('/settings/about-page', [\App\Http\Controllers\Store\AboutPageSettingController::class, 'edit'])->name('settings.about-page');
    Route::put('/settings/about-page', [\App\Http\Controllers\Store\AboutPageSettingController::class, 'update'])->name('settings.about-page.update');

    Route::get('/settings/contact-page', [\App\Http\Controllers\Store\ContactPageSettingController::class, 'edit'])->name('settings.contact-page');
    Route::put('/settings/contact-page', [\App\Http\Controllers\Store\ContactPageSettingController::class, 'update'])->name('settings.contact-page.update');

    Route::get('/settings/quick-pos', [\App\Http\Controllers\Store\QuickPosSettingController::class, 'edit'])->name('settings.quick-pos');
    Route::put('/settings/quick-pos', [\App\Http\Controllers\Store\QuickPosSettingController::class, 'update'])->name('settings.quick-pos.update');
    Route::post('/settings/quick-pos/connect', [\App\Http\Controllers\Store\QuickPosSettingController::class, 'connectToServer'])->name('settings.quick-pos.connect');

    Route::resource('enquiries', \App\Http\Controllers\Store\EnquiryController::class)->only(['index', 'show', 'destroy'])->names([
        'index' => 'store.enquiries.index',
        'show' => 'store.enquiries.show',
        'destroy' => 'store.enquiries.destroy',
    ]);
    Route::resource('settings/legal', \App\Http\Controllers\Store\LegalPageSettingController::class)->names([
        'index' => 'settings.legal.index',
        'create' => 'settings.legal.create',
        'store' => 'settings.legal.store',
        'edit' => 'settings.legal.edit',
        'update' => 'settings.legal.update',
        'destroy' => 'settings.legal.destroy',
    ]);
    Route::get('/stocks', [StoreInventoryController::class, 'index'])->name('inventory.index');
    Route::post('/inventory/request', [StoreInventoryController::class, 'requestStock'])->name('inventory.request');
    Route::post('/inventory/request/generate-po', [StoreInventoryController::class, 'generateWarehousePo'])->name('inventory.request.generate-po');
    Route::get('/reports/stock', [StoreInventoryController::class, 'stockReport'])->name('store.reports.stock');
    Route::get('/stocks/requests', [StoreInventoryController::class, 'requests'])->name('inventory.requests');
    Route::get('/inventory/order/create', [StoreInventoryController::class, 'createOrderInventory'])->name('inventory.order.create');
    Route::post('/inventory/order/store', [StoreInventoryController::class, 'storeOrderInventory'])->name('inventory.order.store');
    Route::get('/inventory/search-products', [StoreInventoryController::class, 'searchProducts'])->name('inventory.search-products');
    Route::get('/inventory/requests/{id}', [StoreInventoryController::class, 'showRequest'])->name('inventory.requests.show');
    Route::get('/inventory/history/{id}', [StoreInventoryController::class, 'history'])->name('inventory.history');
    Route::post('/inventory/requests/{id}/review', [StoreInventoryController::class, 'reviewRequest'])->name('inventory.requests.review');
    Route::post('/inventory/requests/{id}/approve', [StoreInventoryController::class, 'approveRequest'])->name('inventory.requests.approve');
    Route::post('/inventory/requests/{id}/receive', [StoreInventoryController::class, 'processReceive'])->name('inventory.requests.receive');
    Route::post('/inventory/requests/upload-proof', [StoreInventoryController::class, 'uploadPaymentProof'])->name('inventory.requests.upload_proof');
    Route::delete('/inventory/requests/{id}', [StoreInventoryController::class, 'cancelRequest'])->name('inventory.requests.destroy');
    Route::get('/stocks/requests/sample', [StoreInventoryController::class, 'downloadSampleCsv'])->name('inventory.requests.sample');
    Route::post('/stocks/requests/import', [StoreInventoryController::class, 'importStockRequests'])->name('inventory.requests.import');
    Route::get('/stocks/adjustments', [StoreInventoryController::class, 'adjustments'])->name('inventory.adjustments');
    Route::post('/stocks/adjustments', [StoreInventoryController::class, 'storeAdjustment'])->name('inventory.adjustments.store');
    Route::post('/stocks/convert', [StoreInventoryController::class, 'convertWeight'])->name('inventory.convert');

    Route::prefix('store/stock-control')->name('store.stock-control.')->group(function () {
        Route::get('/overview', [StoreStockControlController::class, 'overview'])->name('overview');
        Route::get('/overview/data', [StoreStockControlController::class, 'overviewData'])->name('overview.data');
        Route::patch('recall/{id}/update-status', [StoreRecallController::class, 'updateStatus'])
            ->name('recall.update-status');
        Route::get('/requests', [StoreStockControlController::class, 'requests'])->name('requests');
        Route::get('/requests/create', [StoreStockControlController::class, 'create'])->name('requests.create');
        Route::post('/requests/store', [StoreStockControlController::class, 'store'])->name('requests.store');
        Route::post('/requests/generate-replenishment', [StoreStockControlController::class, 'generateReplenishment'])->name('generate-replenishment');
        Route::get('/requests/{id}', [StoreStockControlController::class, 'show'])->name('requests.show');
        Route::delete('/requests/{id}', [StoreStockControlController::class, 'destroy'])->name('requests.destroy');
        Route::get('/requests/search-products', [StoreStockControlController::class, 'searchProducts'])->name('search-products');
        Route::post('/requests/estimate-pallets', [StoreStockControlController::class, 'estimatePallets'])->name('estimate-pallets');

        Route::get('/received', [StoreStockControlController::class, 'received'])->name('received');
        Route::get('/requests/{id}/receive', [StoreStockControlController::class, 'receive'])->name('requests.receive');
        Route::post('/received/{id}/confirm', [StoreStockControlController::class, 'confirmReceived'])->name('received.confirm');

        Route::get('/low-stock', [StoreStockControlController::class, 'lowStock'])->name('low-stock');
        Route::get('/low-stock/data', [StoreStockControlController::class, 'lowStockData'])->name('low-stock.data');
        Route::post('/low-stock/request', [StoreStockControlController::class, 'quickRequest'])->name('low-stock.request');
        Route::get('/valuation', [StoreStockControlController::class, 'valuation'])->name('valuation');

        Route::get('/expiry', [StoreStockControlController::class, 'expiry'])->name('expiry');
        Route::get('/expiry/data', [StoreStockControlController::class, 'expiryData'])->name('expiry.data');
        Route::get('/expiry', [StoreStockControlController::class, 'expiry'])->name('expiry');
        Route::get('/expiry/data', [StoreStockControlController::class, 'expiryData'])->name('expiry.data');

        Route::post('/recall/{recall}/approve', [StoreRecallController::class, 'approve'])->name('recall.approve');
        Route::post('/recall/{recall}/reject', [StoreRecallController::class, 'reject'])->name('recall.reject');

        Route::get('/recall', [StoreRecallController::class, 'index'])->name('recall.index');
        Route::get('/recall/create', [StoreRecallController::class, 'create'])->name('recall.create');
        Route::post('/recall', [StoreRecallController::class, 'store'])->name('recall.store');
        Route::get('/recall/{recall}', [StoreRecallController::class, 'show'])->name('recall.show');
        Route::post('/recall/{recall}/dispatch', [StoreRecallController::class, 'dispatch'])->name('recall.dispatch');
        Route::get('/recall/{recall}/challan', [StoreRecallController::class, 'downloadChallan'])->name('recall.challan');
    });
    Route::prefix('store')->name('store.')->group(function () {

        Route::resource('promotions', StorePromotionController::class);
        Route::post('/promotions/{id}/status', [StorePromotionController::class, 'updateStatus'])->name('promotions.status');
        Route::get('/audits', [StoreAuditController::class, 'index'])->name('audits.index');
        Route::get('/audits/create', [StoreAuditController::class, 'create'])->name('audits.create');
        Route::post('/audits', [StoreAuditController::class, 'store'])->name('audits.store');
        Route::get('/audits/{id}', [StoreAuditController::class, 'show'])->name('audits.show');
        Route::put('/audits/{id}', [StoreAuditController::class, 'update'])->name('audits.update');
        Route::get('/orders/returns', [StoreReturnController::class, 'index'])->name('sales.returns.index');
        Route::get('/orders/returns/create', [StoreReturnController::class, 'create'])->name('sales.returns.create');
        Route::get('products/{id}/recipe', [StoreRecipeController::class, 'edit'])->name('products.recipe');
        Route::post('products/{id}/recipe', [StoreRecipeController::class, 'store'])->name('products.recipe.store');
        Route::delete('recipe/{id}', [StoreRecipeController::class, 'destroy'])->name('products.recipe.destroy');
        Route::post('/orders/returns', [StoreReturnController::class, 'store'])->name('sales.returns.store');
        Route::get('/orders/returns/search-invoice', [StoreReturnController::class, 'searchInvoice'])->name('sales.returns.search');
        Route::get('/reports/sales', [StoreSalesController::class, 'salesReport'])->name('reports.sales');
        Route::get('/orders', [StoreSalesController::class, 'orders'])->name('sales.orders');
        Route::get('/orders/{id}', [StoreSalesController::class, 'showOrder'])->name('sales.orders.show');
        Route::patch('/orders/{id}/status', [StoreSalesController::class, 'updateOrderStatus'])->name('sales.orders.update-status');
        
        Route::post('/orders/held/{id}/restore', [StoreSalesController::class, 'restoreHeldOrder'])->name('sales.orders.held.restore');
        Route::delete('/orders/held/{id}', [StoreSalesController::class, 'deleteHeldOrder'])->name('sales.orders.held.delete');
        
        Route::post('/pos/add', [StoreSalesController::class, 'addToCart'])->name('sales.cart.add');
        Route::post('/pos/update', [StoreSalesController::class, 'updateCart'])->name('sales.cart.update');
        Route::post('/pos/remove', [StoreSalesController::class, 'removeCartItem'])->name('sales.cart.remove');
        Route::post('/pos/clear', [StoreSalesController::class, 'clearCart'])->name('sales.cart.clear');
        
        Route::get('/sales/daily', [StoreSalesController::class, 'dailySales'])->name('sales.daily');
        Route::get('/sales/weekly', [StoreSalesController::class, 'weeklySales'])->name('sales.weekly');
        Route::post('/pos/checkout', [StoreSalesController::class, 'checkout'])->name('sales.checkout');
        Route::get('/pos/checkout', [StoreSalesController::class, 'checkoutPage'])->name('sales.checkout-page');
        Route::post('/pos/create-customer', [StoreSalesController::class, 'storeCustomer'])->name('sales.customers.store');
        Route::get('/pos/search-customers', [StoreSalesController::class, 'searchCustomer'])->name('sales.customers.search');
        Route::get('/pos/search-customers', [StoreSalesController::class, 'searchCustomer'])->name('sales.customers.search');
        Route::get('/pos/terminal-status', [StoreSalesController::class, 'terminalStatus'])->name('sales.terminal-status');
        Route::get('/pos/get-printers', [StoreSalesController::class, 'getPrinters'])->name('sales.get-printers');
        Route::get('/pos/scale-weight', [StoreSalesController::class, 'getWeight'])->name('sales.scale-weight');
        Route::get('/pos/scanner-scan', [StoreSalesController::class, 'getLastScan'])->name('sales.scanner-scan');
        Route::get('/pos/payment/status', [StoreSalesController::class, 'checkPaxStatus'])->name('sales.payment-status');
        Route::post('/pos/payment/initiate', [StoreSalesController::class, 'initiatePaxPayment'])->name('sales.payment-initiate');
        Route::post('/pos/payment/cancel', [StoreSalesController::class, 'cancelPaxPayment'])->name('sales.payment-cancel');
        Route::post('/pos/manual-print', [StoreSalesController::class, 'manualPrint'])->name('sales.manual-print');
        Route::post('/pos/create-customer', [StoreSalesController::class, 'storeCustomer'])->name('sales.customers.store');
        Route::get('/pos/search-customers', [StoreSalesController::class, 'searchCustomer'])->name('sales.customers.search');
        Route::get('products/{id}/analytics', [StoreProductController::class, 'analytics'])->name('products.analytics');
        Route::get('products/{id}/location-inventory', [StoreProductController::class, 'locationInventory'])->name('products.location-inventory');
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
        Route::get('products/generate-upc', [StoreProductController::class, 'generateUpc'])->name('products.generate-upc');
        Route::resource('products', StoreProductController::class);
        Route::post('products/import', [StoreProductController::class, 'import'])->name('products.import');
        Route::get('products/export', [StoreProductController::class, 'export'])->name('products.export');
        Route::post('products/status', [StoreProductController::class, 'updateStatus'])->name('products.status');

        Route::controller(StoreSupportTicketController::class)
            ->prefix('support')
            ->name('support.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{id}', 'show')->name('show');
                Route::post('/{id}/reply', 'reply')->name('reply');
            });


        Route::prefix('purchase-orders')->name('orders.')->group(function () {
            Route::get('/', [StoreOrderController::class, 'index'])->name('index');
            Route::get('/data', [StoreOrderController::class, 'getOrders'])->name('data');
            Route::get('/{id}', [StoreOrderController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [StoreOrderController::class, 'edit'])->name('edit');
            Route::post('/{id}/update', [StoreOrderController::class, 'update'])->name('update');
            Route::get('/{id}/receive', [StoreOrderController::class, 'receive'])->name('receive');
            Route::post('/{id}/confirm', [StoreOrderController::class, 'confirmReceive'])->name('confirm-receive');
        });

        Route::prefix('inventory')->name('inventory.')->group(function () {
            Route::get('/stock-levels', [StoreOrderController::class, 'stockLevels'])->name('stock-levels');
            Route::get('/stock-levels/data', [StoreOrderController::class, 'getStockLevelsData'])->name('stock-levels.data');
            Route::post('/stock-levels/update', [StoreOrderController::class, 'updateStockLevels'])->name('stock-levels.update');
            Route::get('/visibility/{product_id?}', [StoreOrderController::class, 'globalVisibility'])->name('visibility');
        });
    });
});
// routes/web.php ke bottom mein:

// Load Authentication Routes
require __DIR__ . '/auth.php';

// Load Website Module Routes
require __DIR__ . '/website.php';
