<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use App\Models\StoreStock; // Top par add karein
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StoreProductCategoryExport; // Hum niche banayenge
use App\Imports\StoreProductCategoryImport; // Hum niche banayenge

class ProductCategoryController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $storeId = $user->store_id ?? $user->id;

        $query = ProductCategory::where(function($q) use ($storeId) {
            $q->whereNull('store_id') // Global
              ->orWhere('store_id', $storeId); // Local
        });

        // Search
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by Type
        if ($request->has('type') && $request->type != '') {
            if ($request->type == 'warehouse') {
                $query->whereNull('store_id');
            } else {
                $query->whereNotNull('store_id');
            }
        }

        $categories = $query->latest()->paginate(10);

        return view('store.categories.index', compact('categories'));
    }

    public function analytics($id)
{
    $user = Auth::user();
    $storeId = $user->store_id ?? $user->id;
    $category = ProductCategory::findOrFail($id);

    // 1. Total Inventory in this Category
    $stats = StoreStock::where('store_stocks.store_id', $storeId)
        ->join('products', 'store_stocks.product_id', '=', 'products.id')
        ->where('products.category_id', $id)
        ->selectRaw('COUNT(products.id) as total_products, SUM(store_stocks.quantity) as total_qty, SUM(store_stocks.quantity * store_stocks.selling_price) as total_value')
        ->first();

    // 2. Top 5 Products by Stock Quantity in this Category
    $topProducts = StoreStock::where('store_stocks.store_id', $storeId)
        ->join('products', 'store_stocks.product_id', '=', 'products.id')
        ->where('products.category_id', $id)
        ->orderByDesc('store_stocks.quantity')
        ->limit(5)
        ->select('products.product_name', 'store_stocks.quantity')
        ->get();

    $labels = $topProducts->pluck('product_name');
    $data = $topProducts->pluck('quantity');

    return view('store.categories.analytics', compact('category', 'stats', 'labels', 'data'));
}

    public function create()
    {
        return view('store.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:product_categories,code',
        ]);

        $user = Auth::user();
        
        ProductCategory::create([
            'store_id' => $user->store_id ?? $user->id, // Mark as Local
            'name' => $request->name,
            'code' => $request->code,
            'is_active' => true
        ]);

        return redirect()->route('store.categories.index')->with('success', 'Category created successfully.');
    }

    public function edit($id)
    {
        $category = ProductCategory::where('id', $id)
            ->where('store_id', Auth::user()->store_id ?? Auth::user()->id)
            ->firstOrFail();

        return view('store.categories.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $category = ProductCategory::where('id', $id)
            ->where('store_id', Auth::user()->store_id ?? Auth::user()->id)
            ->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:product_categories,code,' . $id,
        ]);

        $category->update([
            'name' => $request->name,
            'code' => $request->code,
        ]);

        return redirect()->route('store.categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy($id)
    {
        $category = ProductCategory::where('id', $id)
            ->where('store_id', Auth::user()->store_id ?? Auth::user()->id)
            ->firstOrFail();

        $category->delete();
        return back()->with('success', 'Category deleted successfully.');
    }

    public function updateStatus(Request $request)
    {
        // Only allow status toggle for Local Categories
        $category = ProductCategory::where('id', $request->id)
            ->where('store_id', Auth::user()->store_id ?? Auth::user()->id)
            ->first();

        if ($category) {
            $category->update(['is_active' => $request->status]);
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 403);
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,csv']);
        Excel::import(new StoreProductCategoryImport, $request->file('file'));
        return back()->with('success', 'Categories imported successfully.');
    }

    public function export()
    {
        return Excel::download(new StoreProductCategoryExport, 'categories.xlsx');
    }
}