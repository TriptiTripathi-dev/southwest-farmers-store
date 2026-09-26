<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Pure route-table check: no database needed (deliberately doesn't use
 * RefreshDatabase -- this repo's migrations can't build a schema alone, since
 * some alter tables warehouse-pos creates, e.g. kitchen_locations). Would have
 * caught /admin/register: a live, unauthenticated route creating StoreUser
 * accounts (removed alongside the two other fully unrouted Store\Auth\*
 * controllers it sat next to).
 */
class RoutesResolveTest extends TestCase
{
    public function test_every_controller_route_points_at_a_real_class_and_method(): void
    {
        $broken = [];

        foreach (Route::getRoutes() as $route) {
            $action = $route->getAction('controller');
            if (!is_string($action)) {
                continue;
            }

            [$class, $method] = str_contains($action, '@') ? explode('@', $action) : [$action, '__invoke'];

            if (!class_exists($class)) {
                $broken[] = "{$route->uri()} -> missing class {$class}";
            } elseif (!method_exists($class, $method)) {
                $broken[] = "{$route->uri()} -> {$class}@{$method} does not exist";
            }
        }

        $this->assertSame([], $broken, implode("\n", $broken));
    }

    public function test_no_controller_file_is_completely_unrouted(): void
    {
        // Guards against exactly what /admin/register turned out to be: a
        // controller nobody routes to any more, forgotten rather than removed.
        $routedClasses = collect(Route::getRoutes())
            ->map(fn ($route) => $route->getAction('controller'))
            ->filter(fn ($action) => is_string($action))
            ->map(fn ($action) => str_contains($action, '@') ? explode('@', $action)[0] : $action)
            ->unique();

        $unrouted = [];
        foreach ((new \Symfony\Component\Finder\Finder())->files()->in(app_path('Http/Controllers'))->name('*.php') as $file) {
            $class = 'App\\Http\\Controllers\\' . str_replace(
                ['/', '.php'],
                ['\\', ''],
                $file->getRelativePathname()
            );

            if (!class_exists($class) || !is_subclass_of($class, \App\Http\Controllers\Controller::class)) {
                continue;
            }
            if ((new \ReflectionClass($class))->isAbstract() || $class === \App\Http\Controllers\Controller::class) {
                continue;
            }
            if ($routedClasses->doesntContain($class)) {
                $unrouted[] = $class;
            }
        }

        $this->assertSame([], $unrouted, "Controllers with no route pointing at them (dead or forgotten):\n" . implode("\n", $unrouted));
    }

    public function test_route_names_are_unique_so_the_routes_can_be_cached(): void
    {
        // The Dockerfile runs `php artisan route:cache` at startup; it refuses
        // two routes with one name (and the deploy then falls back to no caches).
        $names = collect(Route::getRoutes())->map(fn ($route) => $route->getName())->filter();
        $duplicates = $names->countBy()->filter(fn ($count) => $count > 1)->keys()->all();

        $this->assertSame([], $duplicates, 'Route names used more than once: ' . implode(', ', $duplicates));
        Route::getRoutes()->toSymfonyRouteCollection(); // what route:cache does; throws on a duplicate name
    }

    public function test_pos_product_search_keeps_both_urls(): void
    {
        $this->assertSame(url('/pos/search-products'), route('store.sales.search'));
        $this->assertSame(url('/pos/search'), route('store.sales.search.alias'));

        foreach (['/pos/search', '/pos/search-products'] as $uri) {
            $route = Route::getRoutes()->match(\Illuminate\Http\Request::create($uri));
            $this->assertSame(\App\Http\Controllers\Store\StoreSalesController::class . '@searchProduct', $route->getActionName(), $uri);
        }
    }

    public function test_a_cashier_can_use_both_pos_search_urls(): void
    {
        $cashier = \Mockery::mock(\App\Models\StoreUser::class)->makePartial();
        $cashier->shouldReceive('hasRole')->with('Cashier')->andReturn(true);
        \Illuminate\Support\Facades\Auth::shouldReceive('user')->andReturn($cashier);

        $through = function (string $uri) {
            $request = \Illuminate\Http\Request::create($uri);
            $route = Route::getRoutes()->match($request);
            $request->setRouteResolver(fn () => $route);

            return (new \App\Http\Middleware\CheckCashierRole())->handle($request, fn () => response('passed'));
        };

        $this->assertSame('passed', $through('/pos/search')->getContent());
        $this->assertSame('passed', $through('/pos/search-products')->getContent());
        // Still restricted everywhere else.
        $this->assertTrue($through('/store/dashboard')->isRedirect(route('store.sales.pos')));
    }
}
