<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Website\HomeController;
use App\Http\Controllers\Website\ProductController;
use App\Http\Controllers\Website\CartController;
use App\Http\Controllers\Website\CustomerAuthController;

// Ye code apne routes file mein (guest middleware ke andar) add karein
Route::middleware('guest:customer')->group(function () {
    // Customer Auth Routes
    Route::get('customer/login', [CustomerAuthController::class, 'showLogin'])->name('website.login');
    Route::post('customer/login', [CustomerAuthController::class, 'login'])->name('website.login.post');
    Route::get('customer/register', [CustomerAuthController::class, 'showRegister'])->name('website.register');
    Route::post('customer/register', [CustomerAuthController::class, 'register'])->name('website.register.post');
});

Route::middleware('auth:customer')->group(function () {
    Route::get('dashboard', [CustomerAuthController::class, 'dashboard'])->name('website.dashboard');
    Route::post('logout', [CustomerAuthController::class, 'logout'])->name('website.logout');
});

// Public Website Routes
Route::name('website.')->group(function () {

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
});
