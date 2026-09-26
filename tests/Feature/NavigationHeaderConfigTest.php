<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Client 9/11 list, item 10: every page's title and breadcrumb must match
 * the left navigation. config/navigation.php drives <x-page-header />; these
 * checks keep it in step with the sidebar. No database needed.
 */
class NavigationHeaderConfigTest extends TestCase
{
    private function sidebar(): string
    {
        return file_get_contents(resource_path('views/layouts/partials/sidebar.blade.php'));
    }

    public function test_every_configured_page_is_a_real_route(): void
    {
        foreach (array_keys(config('navigation')) as $name) {
            $this->assertTrue(Route::has($name), "config/navigation.php has unknown route [{$name}]");
        }
    }

    public function test_every_sidebar_link_has_a_page_header_entry(): void
    {
        preg_match_all("/href=\"\\{\\{\\s*route\\('([^']+)'\\)\\s*\\}\\}\"/", $this->sidebar(), $m);
        // The POS register is a full-screen till with no page header by design.
        $missing = array_diff(array_unique($m[1]), array_keys(config('navigation')), ['store.sales.pos']);

        $this->assertSame([], array_values($missing), 'Sidebar links without a config/navigation.php entry');
    }

    public function test_titles_and_sections_are_the_sidebars_own_labels(): void
    {
        // Normalise whitespace and &amp; so "Daily Availability &amp; Calendar" matches.
        $sidebar = preg_replace('/\s+/', ' ', html_entity_decode(strip_tags($this->sidebar())));

        foreach (config('navigation') as $name => $page) {
            $this->assertStringContainsString($page['title'], $sidebar, "[{$name}] title is not a sidebar label");
            if (! empty($page['section'])) {
                $this->assertStringContainsString($page['section'], $sidebar, "[{$name}] section is not a sidebar label");
            }
        }
    }

    public function test_header_renders_title_and_breadcrumb_from_the_current_route(): void
    {
        // Act as if the request is on the Inventory adjustment page.
        $router = $this->app['router'];
        $current = new \ReflectionProperty($router, 'current');
        $current->setValue($router, Route::getRoutes()->getByName('inventory.adjustments'));

        $html = $this->blade('<x-page-header><button>Make Adjustment</button></x-page-header>');

        $html->assertSeeInOrder(['Dashboard', 'Inventory control', 'Inventory adjustment'])
            ->assertSee('page-breadcrumb-link', false)
            ->assertSee('Manually correct stock levels', false)
            ->assertSee('<button>Make Adjustment</button>', false);
    }
}
