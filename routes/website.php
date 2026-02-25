<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Website\HomeController;
use App\Http\Controllers\Website\ProductController;
use App\Http\Controllers\Website\CartController;
use App\Http\Controllers\Website\CustomerAuthController;

// Website Auth Routes (Nested in website. name group for view compatibility)
Route::name('website.')->group(function () {
    
    Route::middleware('guest:customer')->group(function () {
        // Registration Routes
        Route::get('customer/register', [CustomerAuthController::class, 'showRegistrationForm'])->name('register');
        Route::post('customer/register', [CustomerAuthController::class, 'register'])->name('register.post');

        // Login Routes
        Route::get('customer/login', [CustomerAuthController::class, 'showLoginForm'])->name('login');
        Route::post('customer/login', [CustomerAuthController::class, 'login'])->name('login.post');
    });

    Route::middleware('auth:customer')->group(function () {
        Route::get('dashboard', [CustomerAuthController::class, 'dashboard'])->name('dashboard');
        Route::post('customer/logout', [CustomerAuthController::class, 'logout'])->name('logout');
    });

    // Public Website Routes
    // Homepage
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // Products
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/product/{slug}', [ProductController::class, 'show'])->name('products.show');
    Route::get('/quick-shop', [ProductController::class, 'pos'])->name('products.pos');

    // Contact
    Route::get('/contact', [HomeController::class, 'contact'])->name('contact');

    // Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
});

// Alias for old customer.* routes if they are used elsewhere
Route::get('customer/login', [CustomerAuthController::class, 'showLoginForm'])->name('customer.login');
Route::post('customer/login', [CustomerAuthController::class, 'login']);
Route::get('customer/register', [CustomerAuthController::class, 'showRegistrationForm'])->name('customer.register');
Route::post('customer/register', [CustomerAuthController::class, 'register']);
Route::post('customer/logout', [CustomerAuthController::class, 'logout'])->name('customer.logout');
