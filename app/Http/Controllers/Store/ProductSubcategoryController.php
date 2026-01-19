<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use App\Models\StoreStock; // Top par add karein
use Illuminate\Support\Facades\DB;
use App\Models\ProductSubcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StoreProductSubcategoryExport;
use App\Imports\StoreProductSubcategoryImport;

class ProductSubcategoryController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $storeId = $user->store_id ?? $user->id;

        $query = ProductSubcategory::with('category')->where(function($q) use ($storeId) {
            $q->whereNull('store_id') // Global
              ->orWhere('store_id', $storeId); // Local
        });

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }

        $subcategories = $query->latest()->paginate(10);
        
        // Fetch categories for filter dropdown
        $categories = ProductCategory::whereNull('store_id')
            ->orWhere('store_id', $storeId)
            ->orderBy('name')
            ->get();

        return view('store.subcategories.index', compact('subcategories', 'categories'));
    }

    public function create()
    {
        $user = Auth::user();
        $storeId = $user->store_id ?? $user->id;

        // Fetch Categories to attach Subcategory to
        $categories = ProductCategory::where(function($q) use ($storeId) {
            $q->whereNull('store_id')->orWhere('store_id', $storeId);
        })->where('is_active', true)->orderBy('name')->get();

        return view('store.subcategories.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:product_categories,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:product_subcategories,code',
        ]);

        $user = Auth::user();
        
        ProductSubcategory::create([
            'store_id' => $user->store_id ?? $user->id,
            'category_id' => $request->category_id,
            'name' => $request->name,
            'code' => $request->code,
            'is_active' => true
        ]);

        return redirect()->route('store.subcategories.index')->with('success', 'Subcategory created successfully.');
    }

    public function edit($id)
    {
        $user = Auth::user();
        $storeId = $user->store_id ?? $user->id;

        $subcategory = ProductSubcategory::where('id', $id)
            ->where('store_id', $storeId)
            ->firstOrFail();

        $categories = ProductCategory::where(function($q) use ($storeId) {
            $q->whereNull('store_id')->orWhere('store_id', $storeId);
        })->where('is_active', true)->orderBy('name')->get();

        return view('store.subcategories.edit', compact('subcategory', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $storeId = $user->store_id ?? $user->id;

        $subcategory = ProductSubcategory::where('id', $id)
            ->where('store_id', $storeId)
            ->firstOrFail();

        $request->validate([
            'category_id' => 'required|exists:product_categories,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:product_subcategories,code,' . $id,
        ]);

        $subcategory->update([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'code' => $request->code,
        ]);

        return redirect()->route('store.subcategories.index')->with('success', 'Subcategory updated successfully.');
    }

    public function destroy($id)
    {
        $subcategory = ProductSubcategory::where('id', $id)
            ->where('store_id', Auth::user()->store_id ?? Auth::user()->id)
            ->firstOrFail();

        $subcategory->delete();
        return back()->with('success', 'Subcategory deleted successfully.');
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,csv']);
        Excel::import(new StoreProductSubcategoryImport, $request->file('file'));
        return back()->with('success', 'Subcategories imported successfully.');
    }

    public function export()
    {
        return Excel::download(new StoreProductSubcategoryExport, 'subcategories.xlsx');
    }
    
    // Helper for AJAX (Used in Product Create Page later)
    public function getByCategory(Request $request)
    {
        $user = Auth::user();
        $storeId = $user->store_id ?? $user->id;

        $subcategories = ProductSubcategory::where('category_id', $request->category_id)
            ->where(function($q) use ($storeId) {
                $q->whereNull('store_id')->orWhere('store_id', $storeId);
            })
            ->where('is_active', true)
            ->select('id', 'name')
            ->get();
            
        return response()->json($subcategories);
    }

    public function analytics($id)
{
    $user = Auth::user();
    $storeId = $user->store_id ?? $user->id;
    $subcategory = ProductSubcategory::findOrFail($id);

    // 1. Stats
    $stats = StoreStock::where('store_stocks.store_id', $storeId)
        ->join('products', 'store_stocks.product_id', '=', 'products.id')
        ->where('products.subcategory_id', $id)
        ->selectRaw('COUNT(products.id) as total_products, SUM(store_stocks.quantity) as total_qty, SUM(store_stocks.quantity * store_stocks.selling_price) as total_value')
        ->first();

    // 2. Top Products Graph
    $topProducts = StoreStock::where('store_stocks.store_id', $storeId)
        ->join('products', 'store_stocks.product_id', '=', 'products.id')
        ->where('products.subcategory_id', $id)
        ->orderByDesc('store_stocks.quantity')
        ->limit(5)
        ->select('products.product_name', 'store_stocks.quantity')
        ->get();

    $labels = $topProducts->pluck('product_name');
    $data = $topProducts->pluck('quantity');

    return view('store.subcategories.analytics', compact('subcategory', 'stats', 'labels', 'data'));
}
}