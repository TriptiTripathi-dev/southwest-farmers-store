<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\StoreDetail;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\StoreUser;

class PreparedMenuTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test menus index page loads and shows store menu.
     */
    public function test_menus_page_loads_correctly()
    {
        // Get or create store
        $store = StoreDetail::firstOrCreate(
            ['store_code' => 'SWF-TST-101'],
            [
                'store_name' => 'SWF - Test Store',
                'email' => 'test@swf.com',
                'phone' => '1234567890',
                'is_active' => true,
                'pos_terminal_status' => 'offline'
            ]
        );

        $category = MenuCategory::create([
            'store_id' => $store->id,
            'name' => 'KITCHEN SPECIALS',
            'is_active' => true
        ]);

        $item = MenuItem::create([
            'store_id' => $store->id,
            'menu_category_id' => $category->id,
            'name' => 'Test Jollof Rice',
            'price' => 9.99,
            'is_active' => true
        ]);

        // Visit index page
        $response = $this->get(route('website.menus.index'));

        $response->assertStatus(200);
        $response->assertSee('KITCHEN SPECIALS');
    }
}
