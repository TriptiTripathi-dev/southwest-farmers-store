<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Website\HomeController;
use App\Http\Controllers\Website\ProductController;
use App\Http\Controllers\Website\CartController;
use App\Http\Controllers\Website\Auth\CustomerAuthController;

// Website Auth Routes
Route::middleware('guest:customer')->group(function () {
    // Registration Routes
    Route::get('customer/register', [CustomerAuthController::class, 'showRegistrationForm'])->name('customer.register');
    Route::post('customer/register', [CustomerAuthController::class, 'register']);

    // Login Routes
    Route::get('customer/login', [CustomerAuthController::class, 'showLoginForm'])->name('customer.login');
    Route::post('customer/login', [CustomerAuthController::class, 'login']);
});

Route::middleware('auth:customer')->group(function () {
    Route::post('customer/logout', [CustomerAuthController::class, 'logout'])->name('customer.logout');
});

// Public Website Routes
Route::name('website.')->group(function () {

    // Homepage
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // Products
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/product/{slug}', [ProductController::class, 'show'])->name('products.show');

    // Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
});
