<?php

namespace App\Providers;

use App\Models\Cart;
use App\Models\StoreNotification;
use App\View\Composers\SidebarComposer;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.partials.header', function ($view) {
            $cartCount = 0;

            if (Auth::check()) {
                // Get active cart for the logged-in user
                $cart = Cart::where('user_id', Auth::id())
                    ->where('store_id', Auth::user()->store_id)
                    ->where('status', 'active')
                    ->first();

                if ($cart) {
                    $cartCount = $cart->items()->sum('quantity');
                }
            }

            $view->with('cartCount', $cartCount);
        });
        View::composer('layouts.partials.header', function ($view) {
            if (Auth::check()) {
                $user = Auth::user();

                $unreadNotifications = StoreNotification::where('user_id', $user->id)
                    ->unread()
                    ->latest()
                    ->take(5)
                    ->get();

                $unreadCount = StoreNotification::where('user_id', $user->id)
                    ->unread()
                    ->count();

                $view->with('headerNotifications', $unreadNotifications)
                    ->with('headerUnreadCount', $unreadCount);
            }
        });
        View::composer('layouts.partials.sidebar', SidebarComposer::class);
        Paginator::useBootstrapFive();
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
