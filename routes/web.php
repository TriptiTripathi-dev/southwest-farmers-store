<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Store\Auth\LoginController;
use App\Http\Controllers\Store\Auth\ForgotPasswordController;
use App\Http\Controllers\Store\Auth\ResetPasswordController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Store\StoreRoleController;
use App\Http\Controllers\Store\StorePermissionController;
use App\Http\Controllers\Store\GeneralSettingController;
use App\Http\Controllers\Store\ProductController;
use App\Http\Controllers\Store\StoreCustomerController;
use App\Http\Controllers\StoreProfileController;
use App\Http\Controllers\Store\StoreInventoryController;
use App\Http\Controllers\Store\StaffController;

/*
|--------------------------------------------------------------------------
| store Authentication
|--------------------------------------------------------------------------
*/

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
    // Inventory Management
    Route::post('/store/update-status', [StoreProfileController::class, 'updateStatus'])
        ->name('store.update-status');
    Route::resource('roles', StoreRoleController::class);
    Route::resource('permissions', StorePermissionController::class);
    Route::resource('staff', StaffController::class);
    Route::post('/staff/update-status', [StaffController::class, 'updateStatus'])->name('staff.update-status');
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');
    Route::get('/settings/general', [GeneralSettingController::class, 'index'])->name('settings.general');
    Route::put('/settings/general', [GeneralSettingController::class, 'update'])->name('settings.update');

    Route::get('/stocks', [StoreInventoryController::class, 'index'])->name('inventory.index');
    Route::post('/inventory/request', [StoreInventoryController::class, 'requestStock'])->name('inventory.request');
    Route::get('/stocks/requests', [StoreInventoryController::class, 'requests'])->name('inventory.requests');
    Route::delete('/inventory/requests/{id}', [StoreInventoryController::class, 'cancelRequest'])->name('inventory.requests.destroy');
    Route::get('/stocks/adjustments', [StoreInventoryController::class, 'adjustments'])->name('inventory.adjustments');
    Route::post('/stocks/adjustments', [StoreInventoryController::class, 'storeAdjustment'])->name('inventory.adjustments.store');
});
