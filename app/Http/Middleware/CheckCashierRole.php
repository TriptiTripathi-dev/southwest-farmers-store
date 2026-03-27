<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class CheckCashierRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Only apply Cashier role check to store staff (User model).
        // StoreCustomers (website buyers) use a separate guard and have no hasRole().
        if ($user instanceof User && $user->hasRole('Cashier')) {
            // Allowed routes for cashier
            $allowedRoutes = [
                'store.sales.pos',
                'store.sales.orders',
                'store.sales.orders.show',
                'store.sales.returns.index',
                'store.sales.returns.create',
                'store.sales.returns.store',
                'store.sales.returns.search',
                'logout',
                'profile.edit',
                'profile.update',
                'profile.password',
                // POS related actions (AJAX/Post)
                'store.sales.cart.add',
                'store.sales.cart.update',
                'store.sales.cart.remove',
                'store.sales.cart.clear',
                'store.sales.checkout',
                'store.sales.checkout-page',
                'store.sales.customers.store',
                'store.sales.customers.search',
                'store.sales.terminal-status',
                'store.sales.get-printers',
                'store.sales.scale-weight',
                'store.sales.scanner-scan',
                'store.sales.payment-status',
                'store.sales.payment-initiate',
                'store.sales.payment-cancel',
                'store.sales.manual-print',
                'store.sales.search',
                'store.notifications.read',
                'store.notifications.readAll',
                'store.notifications.index',
            ];

            $routeName = $request->route() ? $request->route()->getName() : null;

            // If it's a web route and not in allowed list, redirect to POS
            if ($routeName && !in_array($routeName, $allowedRoutes)) {
                return redirect()->route('store.sales.pos')->with('error', 'Access denied. Your account is restricted to POS only.');
            }
        }

        return $next($request);
    }
}
