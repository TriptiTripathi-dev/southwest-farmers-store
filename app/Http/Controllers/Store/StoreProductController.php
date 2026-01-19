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
use App\Models\StockAdjustment;

class StoreProductController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $storeId = $user->store_id ?? $user->id;

        // Query Store Stocks (linked with Products)
       $query = Product::with(['category', 'subcategory','storeStocks']);

        // 1. Search Filter
      if ($request->filled('search')) {
    $search = $request->search;
    $query->where(function($q) use ($search) {
        $q->where('product_name', 'ilike', "%{$search}%")   // Change 'like' to 'ilike'
          ->orWhere('sku', 'ilike', "%{$search}%")          // Change 'like' to 'ilike'
          ->orWhere('barcode', 'ilike', "%{$search}%");     // Change 'like' to 'ilike'
    });
}

        // 2. Status Filter
        if ($request->filled('status')) {
            $isActive = $request->status === 'active' ? 1 : 0;
            $query->where('is_active', $isActive);
        }

        // 3. Category Filter (Added this)
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Fetch Data
        $products = $query->latest()
            ->paginate(10)
            ->withQueryString();

        // Fetch Categories for the Filter Dropdown
        $categories = ProductCategory::select('id', 'name')->get();
        $products = $query->orderBy('product_name')->paginate(10);
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

public function analytics($id)
{
    $user = Auth::user();
    $storeId = $user->store_id ?? $user->id;

    // Fetch Product & Current Stock
    $stock = StoreStock::where('store_id', $storeId)->where('product_id', $id)->with('product')->firstOrFail();
    $product = $stock->product;

    // Consumption Trend (Last 30 Days - Subtract Operations)
    $end = now()->startOfDay(); // Today, but adjust if you want up to now()
    $start = $end->copy()->subDays(10); // 30 days back
    $history = StockAdjustment::where('store_id', $storeId)
        ->where('product_id', $id)
        ->where('operation', 'subtract')
        ->where('created_at', '==', $start)
        ->selectRaw("DATE(created_at) as date, SUM(quantity) as total") // Use DATE() for broader DB compatibility; adjust if needed
        ->groupBy('date')
        ->orderBy('date')
        ->pluck('total', 'date'); // Keyed by date for easy merging

    // Generate all dates in the period
    $period = new \DatePeriod($start, new \DateInterval('P1D'), $end);
    $dates = [];
    $usage = [];
    foreach ($period as $date) {
        $dateStr = $date->format('Y-m-d');
        $dates[] = $dateStr;
        $usage[] = $history->get($dateStr, 0); // Default to 0 if no usage
    }

    return view('store.products.analytics', compact('stock', 'product', 'dates', 'usage'));
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
        $product = Product::where('id', $id)->firstOrFail();
        
        // Check permissions inside view or here?
        // We will allow editing "selling_price" for everyone.
        // Full edit only for Local.

        $categories = ProductCategory::all(); 
        // Note: For full edit, you might want to load subcategories via JS or pass them if Local.
        
        return view('store.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);


        // 2. If Local Product, update other details
        if ($product->store_id != null) {
            $product->update($request->only([
                'product_name', 'description', 'category_id', 'subcategory_id', 'unit', 'barcode'
            ]));
            
            if ($request->hasFile('image')) {
                $product->update(['icon' => $request->file('image')->store('products', 'public')]);
            }
        }else{
            
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