<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\SetStoreMiddleware::class,
            \App\Http\Middleware\CheckCashierRole::class,
        ]);

        $middleware->redirectGuestsTo(function ($request) {
            if ($request->is('customer*') || $request->is('cart*') || $request->is('checkout*') || $request->is('dashboard*') || $request->is('my-orders*')) {
                return route('website.login');
            }
            return route('login');
        });

        $middleware->redirectUsersTo(function ($request) {
            // If visiting customer guest routes while logged in as customer, redirect to website home
            if ($request->is('customer*') && auth('customer')->check()) {
                return route('website.home');
            }

            // If visiting admin guest routes while logged in as store user, redirect to dashboard
            if (($request->is('admin*') || $request->is('store*')) && auth('store')->check()) {
                return route('dashboard');
            }

            // Minimal fallback logic
            if (auth('customer')->check() && !$request->is('admin*') && !$request->is('store*')) {
                return route('website.home');
            }

            return route('dashboard');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
