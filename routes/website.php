<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Website\HomeController;
use App\Http\Controllers\Website\ProductController;
use App\Http\Controllers\Website\CartController;
use App\Http\Controllers\Website\CustomerAuthController;

// Website Routes
Route::name('website.')->group(function () {
    
    // Auth Routes
    Route::middleware('guest:customer')->group(function () {
        // Registration
        Route::get('customer/register', [CustomerAuthController::class, 'showRegistrationForm'])->name('register');
        Route::post('customer/register', [CustomerAuthController::class, 'register'])->name('register.post');

        // Login
        Route::get('customer/login', [CustomerAuthController::class, 'showLoginForm'])->name('login');
        Route::post('customer/login', [CustomerAuthController::class, 'login'])->name('login.post');
    });

    Route::middleware('auth:customer')->group(function () {
        Route::get('dashboard', [CustomerAuthController::class, 'dashboard'])->name('dashboard');
        Route::post('customer/logout', [CustomerAuthController::class, 'logout'])->name('logout');

        // Cart
        Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
        Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
        Route::patch('/cart/{id}', [CartController::class, 'update'])->name('cart.update');
        Route::delete('/cart/{id}', [CartController::class, 'destroy'])->name('cart.destroy');
        Route::post('/cart/apply-coupon', [CartController::class, 'applyCoupon'])->name('cart.coupon');
    });

    // Public Routes
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/product/{slug}', [ProductController::class, 'show'])->name('products.show');
    Route::get('/quick-shop', [ProductController::class, 'pos'])->name('products.pos');
    Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
    Route::post('/contact', [HomeController::class, 'submitContact'])->name('contact.submit');
    Route::get('/about', [HomeController::class, 'about'])->name('about');
});
