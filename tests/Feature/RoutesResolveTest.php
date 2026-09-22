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
}
