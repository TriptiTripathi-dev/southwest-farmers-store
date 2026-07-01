<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\MenuCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MenuItemController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $storeId = $user->store_id ?? $user->id;

        $query = MenuItem::with('category')->where('store_id', $storeId);

        if ($request->has('search')) {
            $query->where('name', 'ilike', '%' . $request->search . '%');
        }

        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('menu_category_id', $request->category_id);
        }

        $items = $query->latest()->paginate(10);
        $categories = MenuCategory::where('store_id', $storeId)->get();

        return view('store.menu-items.index', compact('items', 'categories'));
    }

    public function create()
    {
        $user = Auth::user();
        $storeId = $user->store_id ?? $user->id;
        $categories = MenuCategory::where('store_id', $storeId)->where('is_active', true)->get();

        return view('store.menu-items.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'menu_category_id' => 'required|exists:menu_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();
        $storeId = $user->store_id ?? $user->id;

        // Verify the category belongs to the store
        $category = MenuCategory::where('id', $request->menu_category_id)
            ->where('store_id', $storeId)
            ->firstOrFail();

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('menu_items', 'r2');
        }

        MenuItem::create([
            'store_id' => $storeId,
            'menu_category_id' => $category->id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'image' => $imagePath,
            'is_active' => true,
        ]);

        return redirect()->route('menu-items.index')->with('success', 'Menu item created successfully.');
    }

    public function edit(MenuItem $menuItem)
    {
        $user = Auth::user();
        $storeId = $user->store_id ?? $user->id;

        if ($menuItem->store_id !== $storeId) {
            abort(403);
        }

        $categories = MenuCategory::where('store_id', $storeId)->where('is_active', true)->get();

        return view('store.menu-items.edit', compact('menuItem', 'categories'));
    }

    public function update(Request $request, MenuItem $menuItem)
    {
        $user = Auth::user();
        $storeId = $user->store_id ?? $user->id;

        if ($menuItem->store_id !== $storeId) {
            abort(403);
        }

        $request->validate([
            'menu_category_id' => 'required|exists:menu_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'required|boolean',
        ]);

        // Verify the category belongs to the store
        $category = MenuCategory::where('id', $request->menu_category_id)
            ->where('store_id', $storeId)
            ->firstOrFail();

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('menu_items', 'r2');
            $menuItem->image = $imagePath;
        }

        $menuItem->update([
            'menu_category_id' => $category->id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'is_active' => $request->is_active,
        ]);

        return redirect()->route('menu-items.index')->with('success', 'Menu item updated successfully.');
    }

    public function destroy(MenuItem $menuItem)
    {
        $user = Auth::user();
        $storeId = $user->store_id ?? $user->id;

        if ($menuItem->store_id !== $storeId) {
            abort(403);
        }

        $menuItem->delete();

        return redirect()->route('menu-items.index')->with('success', 'Menu item deleted successfully.');
    }
}
