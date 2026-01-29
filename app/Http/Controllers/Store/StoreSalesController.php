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
use App\Models\StockTransaction;
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

    // ... inside StoreSalesController class ...

    // Display All Orders History
    public function orders(Request $request)
    {
        $storeId = Auth::user()->store_id;

        $query = Sale::where('store_id', $storeId)
            ->with(['customer', 'items']) // Eager load relationships
            ->orderBy('created_at', 'desc');

        // Optional: Simple Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('invoice_number', 'like', "%$search%")
                  ->orWhereHas('customer', function($q) use ($search) {
                      $q->where('name', 'like', "%$search%");
                  });
        }

        $orders = $query->paginate(10);

        return view('store.sales.orders', compact('orders'));
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
        $request->validate([
            'cart' => 'required|json',
            'customer_id' => 'required|exists:store_customers,id',
            'subtotal' => 'required|numeric',
            'gst_amount' => 'required|numeric',
            'tax_amount' => 'required|numeric',
            'discount_amount' => 'required|numeric',
            'total_amount' => 'required|numeric',
        ]);

        $cart = json_decode($request->cart, true);
        if (empty($cart)) {
            return response()->json(['message' => 'Cart is empty'], 400);
        }

        $storeId = Auth::user()->store_id;

        DB::beginTransaction();
        try {
            // Generate Invoice Number (example: INV-YYYYMMDD-XXXX)
            $invoicePrefix = 'INV-' . date('Ymd');
            $lastSale = Sale::where('store_id', $storeId)
                ->whereDate('created_at', today())
                ->orderBy('id', 'desc')
                ->first();
            $seq = $lastSale ? (int)substr($lastSale->invoice_number ?? 'INV-0000-0000', -4) + 1 : 1;
            $invoiceNumber = $invoicePrefix . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);

            // Create Sale
            $sale = Sale::create([
                'store_id' => $storeId,
                'customer_id' => $request->customer_id,
                'invoice_number' => $invoiceNumber,
                'subtotal' => $request->subtotal,
                'gst_amount' => $request->gst_amount,
                'tax_amount' => $request->tax_amount,
                'discount_amount' => $request->discount_amount,
                'total_amount' => $request->total_amount,
                'payment_method' => $request->payment_method, // Later add payment method selection
                'created_by' => Auth::id(),
            ]);

            foreach ($cart as $item) {
                // Create SaleItem
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['price'] * $item['quantity'],
                ]);

                // Deduct from StoreStock
                $storeStock = StoreStock::where('store_id', $storeId)
                    ->where('product_id', $item['id'])
                    ->firstOrFail();

                if ($storeStock->quantity < $item['quantity']) {
                    throw new \Exception('Insufficient stock for ' . $item['name']);
                }

                $storeStock->decrement('quantity', $item['quantity']);

                // Create StockTransaction
                StockTransaction::create([
                    'product_id' => $item['id'],
                    'store_id' => $storeId,
                    'customer_id' => $request->customer_id,
                    'type' => 'sale',
                    'quantity_change' => -$item['quantity'],
                    'running_balance' => $storeStock->quantity,
                    'reference_id' => $sale->id,
                    'remarks' => 'Sale Invoice: ' . $invoiceNumber,
                    'ware_user_id' => Auth::id(), // Assuming store user
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'invoice' => $invoiceNumber,
                'message' => 'Sale completed successfully!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}
