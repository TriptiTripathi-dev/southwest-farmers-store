<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\MenuCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MenuCategoryController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $storeId = $user->store_id ?? $user->id;

        $query = MenuCategory::where('store_id', $storeId);

        if ($request->has('search')) {
            $query->where('name', 'ilike', '%' . $request->search . '%');
        }

        $categories = $query->latest()->paginate(10);

        return view('store.menu-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('store.menu-categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();
        $storeId = $user->store_id ?? $user->id;

        $imagePath = null;
        if ($request->hasFile('image')) {
            try {
                $imagePath = $request->file('image')->store('menu_categories', 'r2');
            } catch (\Exception $e) {
                return back()->withInput()->with('error', 'Cloud storage upload failed: ' . $e->getMessage());
            }
        }

        MenuCategory::create([
            'store_id' => $storeId,
            'name' => $request->name,
            'description' => $request->description,
            'image' => $imagePath,
            'is_active' => true,
        ]);

        return redirect()->route('menu-categories.index')->with('success', 'Menu category created successfully.');
    }

    public function edit(MenuCategory $menuCategory)
    {
        $user = Auth::user();
        $storeId = $user->store_id ?? $user->id;

        if ($menuCategory->store_id !== $storeId) {
            abort(403);
        }

        return view('store.menu-categories.edit', compact('menuCategory'));
    }

    public function update(Request $request, MenuCategory $menuCategory)
    {
        $user = Auth::user();
        $storeId = $user->store_id ?? $user->id;

        if ($menuCategory->store_id !== $storeId) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'required|boolean',
        ]);

        if ($request->hasFile('image')) {
            try {
                $imagePath = $request->file('image')->store('menu_categories', 'r2');
                $menuCategory->image = $imagePath;
            } catch (\Exception $e) {
                return back()->withInput()->with('error', 'Cloud storage upload failed: ' . $e->getMessage());
            }
        }

        $menuCategory->update([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->is_active,
        ]);

        return redirect()->route('menu-categories.index')->with('success', 'Menu category updated successfully.');
    }

    public function destroy(MenuCategory $menuCategory)
    {
        $user = Auth::user();
        $storeId = $user->store_id ?? $user->id;

        if ($menuCategory->store_id !== $storeId) {
            abort(403);
        }

        $menuCategory->delete();

        return redirect()->route('menu-categories.index')->with('success', 'Menu category deleted successfully.');
    }
}
