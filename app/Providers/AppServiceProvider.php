<?php

namespace App\Providers;

use App\Models\Cart;
use App\Models\StoreNotification;
use App\View\Composers\SidebarComposer;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        // Favicon: the one uploaded under Settings > General Settings, falling
        // back to public/logo.png (QA: "Store side favicon" -- the layouts
        // ignored the uploaded favicon and always used the hard-coded file).
        View::composer(['layouts.app', 'layouts.guest', 'components.website-layout'], function ($view) {
            static $favicon = null;
            if ($favicon === null) {
                $favicon = ['url' => asset('logo.png'), 'type' => 'image/jpeg', 'v' => filemtime(public_path('logo.png')) ?: 1];
                try {
                    $settings = \App\Models\StoreSetting::first();
                    if ($settings && $settings->favicon) {
                        $ext = strtolower(pathinfo($settings->favicon, PATHINFO_EXTENSION));
                        $favicon = [
                            'url' => \Illuminate\Support\Facades\Storage::disk('r2')->url($settings->favicon),
                            'type' => match ($ext) { 'png' => 'image/png', 'ico' => 'image/x-icon', 'svg' => 'image/svg+xml', 'webp' => 'image/webp', default => 'image/jpeg' },
                            'v' => optional($settings->updated_at)->timestamp ?? 1,
                        ];
                    }
                } catch (\Throwable $e) {
                    // keep the fallback
                }
            }
            $view->with('appFavicon', $favicon);
        });

        // UTC timestamp -> store local time, for display only (client PDF
        // 9/22, Store item 5: an order placed at 11:23am showed 04:23 PM).
        $storeTime = function () {
            return $this->copy()->setTimezone(config('app.display_timezone', 'UTC'));
        };
        \Illuminate\Support\Carbon::macro('storeTime', $storeTime);
        \Carbon\Carbon::macro('storeTime', $storeTime);
        \Carbon\CarbonImmutable::macro('storeTime', $storeTime);

        // Local .env files point at the shared Railway database, so migrate:fresh /
        // db:wipe / rollback must be refused for it even when APP_ENV=local. Keyed
        // off the default connection's host so the sqlite test database is unaffected.
        $defaultHost = (string) config('database.connections.' . config('database.default') . '.host');
        DB::prohibitDestructiveCommands($this->app->isProduction() || str_contains($defaultHost, 'rlwy.net'));

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
