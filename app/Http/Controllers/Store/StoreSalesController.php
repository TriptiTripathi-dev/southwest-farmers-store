<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StoreStock;
use App\Models\ProductCategory; // Assuming you have this model
use App\Models\StoreCustomer; // Assuming you have this model
use App\Models\Product;
use App\Services\StoreStockService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StoreSalesController extends Controller
{
    public function index()
    {
        // Fetch Categories for the filter list
        $categories = ProductCategory::select('id', 'name')->orderBy('name')->get();
        return view('store.sales.pos', compact('categories'));
    }


    public function searchProduct(Request $request)
    {
        $term = $request->term;
        $category = $request->category;
        $storeId = Auth::user()->store_id;

        $query = StoreStock::query()
            // FIX: Specify table name to avoid ambiguity
            ->where('store_stocks.store_id', $storeId)
            ->where('store_stocks.quantity', '>', 0)
            ->join('products', 'store_stocks.product_id', '=', 'products.id')
            ->leftJoin('product_categories', 'products.category_id', '=', 'product_categories.id')
            ->select(
                'store_stocks.product_id',
                'store_stocks.quantity',
                'products.product_name',
                'products.sku',
                'products.price',
                'products.icon',
                'product_categories.name as category_name'
            );

        // Filter by Category
        if ($category && $category !== 'all') {
            $query->where('product_categories.slug', $category);
        }

        // Search Term
        if ($term) {
            $query->where(function ($q) use ($term) {
                $q->where('products.product_name', 'ILIKE', "%{$term}%")
                    ->orWhere('products.sku', 'ILIKE', "%{$term}%");
            });
        }

        $products = $query->orderBy('store_stocks.quantity', 'asc')
            ->orderBy('products.product_name', 'asc')
            ->limit(20)
            ->get();

        return response()->json($products);
    }

    // 2. Customer Search (PostgreSQL ILIKE)
    public function searchCustomer(Request $request)
    {
        $term = $request->term;

        $customers = StoreCustomer::where('store_id', Auth::user()->store_id)
            ->where(function ($q) use ($term) {
                $q->where('name', 'ILIKE', "%{$term}%")
                    ->orWhere('phone', 'ILIKE', "%{$term}%")
                    ->orWhere('email', 'ILIKE', "%{$term}%");
            })
            ->limit(10)
            ->get();

        return response()->json($customers);
    }

    // 3. Create New Customer (AJAX)
    public function storeCustomer(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email'
        ]);

        $customer = StoreCustomer::create([
            'store_id' => Auth::user()->store_id,
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'loyalty_points' => 0 // Default
        ]);

        return response()->json(['success' => true, 'customer' => $customer]);
    }

    // 4. Checkout (Existing FIFO Logic)
    public function checkout(Request $request, StoreStockService $stockService)
    {
        // ... (Keep your existing checkout logic here) ...
        // Ensure you save 'customer_id' in the Sale model if a customer is selected
    }
}
