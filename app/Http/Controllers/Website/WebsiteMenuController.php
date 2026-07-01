<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\StoreDetail;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use Illuminate\Http\Request;

class WebsiteMenuController extends Controller
{
    /**
     * Get the active store ID based on session or coordinates.
     */
    protected function getActiveStoreId()
    {
        // 1. Get from session
        if (session()->has('store_id')) {
            return session('store_id');
        }

        // 2. Check if customer is logged in and has default store
        if (auth('customer')->check()) {
            $customer = auth('customer')->user();
            if ($customer->store_id) {
                session(['store_id' => $customer->store_id]);
                return $customer->store_id;
            }
        }

        // 3. Fallback to the first active store
        $firstStore = StoreDetail::where('is_active', true)->first();
        if ($firstStore) {
            session(['store_id' => $firstStore->id]);
            return $firstStore->id;
        }

        return null;
    }

    /**
     * Display prepared menus catalog for the nearest store.
     */
    public function index(Request $request)
    {
        $storeId = $this->getActiveStoreId();
        
        $currentStore = $storeId ? StoreDetail::find($storeId) : null;
        
        $categories = [];
        $menuItems = [];
        
        if ($storeId) {
            $categories = MenuCategory::where('store_id', $storeId)
                ->where('is_active', true)
                ->get();
                
            $menuItems = MenuItem::with('category')
                ->where('store_id', $storeId)
                ->where('is_active', true)
                ->get()
                ->groupBy('menu_category_id');
        }

        return view('website.menus.index', compact('categories', 'menuItems', 'currentStore'));
    }

    /**
     * Display prepared menu items inside a specific category.
     */
    public function category($id)
    {
        $storeId = $this->getActiveStoreId();
        
        $currentStore = $storeId ? StoreDetail::find($storeId) : null;
        $category = MenuCategory::findOrFail($id);
        
        if ($category->store_id !== $storeId) {
            abort(404);
        }

        $items = MenuItem::where('menu_category_id', $category->id)
            ->where('is_active', true)
            ->paginate(12);

        return view('website.menus.category', compact('category', 'items', 'currentStore'));
    }
}
