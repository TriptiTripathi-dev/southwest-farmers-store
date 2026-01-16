<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\StoreStock;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StoreProductController extends Controller
{
    /**
     * 1. Product Listing Page (Inventory)
     * Source of Truth: StoreStocks Table
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $storeId = $user->store_id ?? $user->id; // Adjust based on your Auth logic

        $query = StoreStock::where('store_id', $storeId)
            ->with('product') // Eager load product details
            ->join('products', 'store_stocks.product_id', '=', 'products.id') // Join for sorting/searching
            ->select('store_stocks.*'); // Select stock fields primarily

        // Search Logic
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('product', function($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $stocks = $query->orderBy('products.product_name', 'asc')->paginate(10);

        return view('inventory.index', compact('stocks'));
    }

    /**
     * Show Create/Import Page
     */
    public function create()
    {
        $user = Auth::user();
        $storeId = $user->store_id ?? $user->id;

        // Fetch Global Products that are NOT yet in this store's stock
        $globalProducts = Product::global()
            ->where('is_active', true)
            ->whereDoesntHave('storeStocks', function($q) use ($storeId) {
                $q->where('store_id', $storeId);
            })
            ->get();

        // Fetch categories for Local Product creation
        $categories = \App\Models\ProductCategory::where('is_active', true)->get();

        return view('store.inventory.create', compact('globalProducts', 'categories'));
    }

    /**
     * Store Logic (Handles both Tab A and Tab B)
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $storeId = $user->store_id ?? $user->id;

        if ($request->type === 'import') {
            // --- TAB A: IMPORT GLOBAL ---
            $request->validate([
                'product_id' => 'required|exists:products,id',
                'selling_price' => 'required|numeric|min:0'
            ]);

            StoreStock::create([
                'store_id' => $storeId,
                'product_id' => $request->product_id,
                'quantity' => 0, // Default start
                'selling_price' => $request->selling_price
            ]);

            return redirect()->route('store.inventory.index')->with('success', 'Global product added to inventory.');

        } else {
            // --- TAB B: CREATE LOCAL ---
            $request->validate([
                'product_name' => 'required|string|max:255',
                'sku' => 'required|string|unique:products,sku',
                'category_id' => 'required|exists:product_categories,id',
                'selling_price' => 'required|numeric|min:0',
                'image' => 'nullable|image|max:2048'
            ]);

            // 1. Create Product (Local)
            $productData = $request->only(['product_name', 'sku', 'description', 'category_id']);
            $productData['store_id'] = $storeId; // Mark as Local
            $productData['is_active'] = 1;
            
            // Handle Image
            if ($request->hasFile('image')) {
                $productData['image'] = $request->file('image')->store('products', 'public');
            }

            $product = Product::create($productData);

            // 2. Create Stock Entry
            StoreStock::create([
                'store_id' => $storeId,
                'product_id' => $product->id,
                'quantity' => 0,
                'selling_price' => $request->selling_price
            ]);

            return redirect()->route('store.inventory.index')->with('success', 'Local product created and added.');
        }
    }

    /**
     * Edit Page
     */
    public function edit($id)
    {
        // Find Stock entry, ensuring it belongs to this store
        $stock = StoreStock::where('id', $id)
            ->where('store_id', Auth::user()->store_id ?? Auth::user()->id)
            ->with('product')
            ->firstOrFail();
            
        return view('store.inventory.edit', compact('stock'));
    }

    /**
     * Update Logic (Restricted based on Global/Local)
     */
    public function update(Request $request, $id)
    {
        $stock = StoreStock::where('id', $id)->firstOrFail();
        $product = $stock->product;

        // Validation Common to both
        $request->validate([
            'selling_price' => 'required|numeric|min:0',
        ]);

        // Update Stock Price (Allowed for everyone)
        $stock->update(['selling_price' => $request->selling_price]);

        // Logic Split
        if ($product->is_global) {
            // GLOBAL PRODUCT: Do not update name/image/description
            // Only stock price updated above.
            $msg = "Selling price updated.";
        } else {
            // LOCAL PRODUCT: Allow full update
            $request->validate([
                'product_name' => 'required|string|max:255',
                'description' => 'nullable|string',
            ]);

            $prodData = $request->only(['product_name', 'description']);

            if ($request->hasFile('image')) {
                $prodData['image'] = $request->file('image')->store('products', 'public');
            }

            $product->update($prodData);
            $msg = "Local product details updated.";
        }

        return redirect()->route('store.inventory.index')->with('success', $msg);
    }
}