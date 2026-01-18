<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductSubcategory;
use App\Models\StoreStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StoreProductExport;
use App\Imports\StoreProductImport;

class StoreProductController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $storeId = $user->store_id ?? $user->id;

        // Query Store Stocks (linked with Products)
        $query = StoreStock::where('store_stocks.store_id', $storeId)
            ->join('products', 'store_stocks.product_id', '=', 'products.id')
            ->select('store_stocks.*', 'products.store_id as product_store_id', 'products.product_name', 'products.sku', 'products.image', 'products.is_active as product_status');

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('products.product_name', 'like', "%{$search}%")
                  ->orWhere('products.sku', 'like', "%{$search}%");
            });
        }

        // Filter by Type
        if ($request->has('type') && $request->type != '') {
            if ($request->type == 'warehouse') {
                $query->whereNull('products.store_id');
            } else {
                $query->whereNotNull('products.store_id');
            }
        }

        $products = $query->orderBy('products.product_name')->paginate(10);
        $categories = ProductCategory::where('is_active', true)->get(); // For Import Modal

        return view('store.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $user = Auth::user();
        $storeId = $user->store_id ?? $user->id;
        
        // Fetch Categories (Global + Local)
        $categories = ProductCategory::whereNull('store_id')
            ->orWhere('store_id', $storeId)
            ->where('is_active', true)
            ->get();

        return view('store.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string|max:255',
            'sku' => 'required|unique:products,sku',
            'category_id' => 'required',
            'selling_price' => 'required|numeric',
        ]);

        $user = Auth::user();
        $storeId = $user->store_id ?? $user->id;

        // 1. Create Local Product
        $product = Product::create([
            'store_id' => $storeId,
            'category_id' => $request->category_id,
            'subcategory_id' => $request->subcategory_id,
            'product_name' => $request->product_name,
            'sku' => $request->sku,
            'barcode' => $request->barcode,
            'unit' => $request->unit ?? 'pcs',
            'price' => $request->selling_price, // Base price same as selling
            'icon' => $request->hasFile('image') ? $request->file('image')->store('products', 'public') : null,
            'is_active' => true
        ]);

        // 2. Add to Stock
        StoreStock::create([
            'store_id' => $storeId,
            'product_id' => $product->id,
            'quantity' => 0,
            'selling_price' => $request->selling_price
        ]);

        return redirect()->route('store.products.index')->with('success', 'Product created successfully.');
    }

    public function edit($id)
    {
        // $id here is the store_stocks.id (from the list)
        $stock = StoreStock::where('id', $id)->with('product')->firstOrFail();
        
        // Check permissions inside view or here?
        // We will allow editing "selling_price" for everyone.
        // Full edit only for Local.

        $categories = ProductCategory::all(); 
        // Note: For full edit, you might want to load subcategories via JS or pass them if Local.
        
        return view('store.products.edit', compact('stock', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $stock = StoreStock::findOrFail($id);
        $product = $stock->product;

        $request->validate(['selling_price' => 'required|numeric']);

        // 1. Always update Selling Price
        $stock->update(['selling_price' => $request->selling_price]);

        // 2. If Local Product, update other details
        if ($product->store_id != null) {
            $product->update($request->only([
                'product_name', 'description', 'category_id', 'subcategory_id', 'unit', 'barcode'
            ]));
            
            if ($request->hasFile('image')) {
                $product->update(['icon' => $request->file('image')->store('products', 'public')]);
            }
        }

        return redirect()->route('store.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy($id)
    {
        $stock = StoreStock::findOrFail($id);
        $product = $stock->product;

        // Only allow delete if it's a Local Product
        if ($product->store_id != null) {
            $stock->delete(); // Remove stock entry
            $product->delete(); // Soft delete product
            return back()->with('success', 'Local product deleted.');
        }

        return back()->with('error', 'Cannot delete Warehouse products.');
    }

    public function updateStatus(Request $request)
    {
        // Only for Local Products
        $product = Product::find($request->id);
        if ($product && $product->store_id != null) {
            $product->update(['is_active' => $request->status]);
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 403);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv',
            'category_id' => 'required',
            'subcategory_id' => 'required'
        ]);

        // Pass Category/Subcategory IDs to the Import Class
        Excel::import(new StoreProductImport(
            $request->category_id, 
            $request->subcategory_id
        ), $request->file('file'));

        return back()->with('success', 'Products imported successfully.');
    }

    public function export()
    {
        return Excel::download(new StoreProductExport, 'products.xlsx');
    }
}