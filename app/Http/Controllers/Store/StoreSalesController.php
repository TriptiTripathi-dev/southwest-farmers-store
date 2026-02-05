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
use App\Models\Cart;
use App\Models\CartItem;


class StoreSalesController extends Controller
{
    public function index()
    {  
         $categories = ProductCategory::select('id', 'name')->orderBy('name')->get();

        // Fetch active cart for logged in user
        $currentCart = Cart::where('user_id', Auth::id())
            ->where('store_id', Auth::user()->store_id)
            ->where('status', 'active')
            ->with('items.product')
            ->first();
        
        return view('store.sales.pos', compact('categories', 'currentCart'));
    }

    // ... inside StoreSalesController class ...

    // SALES REPORT (Dynamic with Filters)
    public function salesReport(Request $request)
    {
        $storeId = Auth::user()->store_id;
        
        // Default: Current Month
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));
        $paymentMethod = $request->input('payment_method');

        // Base Query
        $query = Sale::where('store_id', $storeId)
                     ->whereDate('created_at', '>=', $startDate)
                     ->whereDate('created_at', '<=', $endDate);

        // Optional Filter: Payment Method
        if ($paymentMethod && $paymentMethod !== 'all') {
            $query->where('payment_method', $paymentMethod);
        }

        // Calculate Summaries (cloning to prevent query reset)
        $summaryQuery = clone $query;
        $totalRevenue = $summaryQuery->sum('total_amount');
        $totalTax = $summaryQuery->sum('tax_amount'); // or gst_amount depending on your column usage
        $totalDiscount = $summaryQuery->sum('discount_amount');
        $totalOrders = $summaryQuery->count();

        // Get Paginated Results
        $sales = $query->with('customer')->latest()->paginate(20)->withQueryString();

        return view('store.reports.sales', compact(
            'sales', 'totalRevenue', 'totalTax', 'totalDiscount', 'totalOrders', 
            'startDate', 'endDate', 'paymentMethod'
        ));
    }
    public function dailySales(Request $request)
    {
        $storeId = Auth::user()->store_id;
        $date = $request->input('date', date('Y-m-d'));

        // Fetch transactions for the specific date
        $sales = Sale::where('store_id', $storeId)
            ->whereDate('created_at', $date)
            ->with('customer')
            ->latest()
            ->paginate(15);

        // Calculate Totals
        $totalRevenue = Sale::where('store_id', $storeId)->whereDate('created_at', $date)->sum('total_amount');
        $totalOrders = Sale::where('store_id', $storeId)->whereDate('created_at', $date)->count();
        $cashSales = Sale::where('store_id', $storeId)->whereDate('created_at', $date)->where('payment_method', 'cash')->sum('total_amount');
        $digitalSales = Sale::where('store_id', $storeId)->whereDate('created_at', $date)->where('payment_method', '!=', 'cash')->sum('total_amount');

        return view('store.sales.daily', compact('sales', 'totalRevenue', 'totalOrders', 'cashSales', 'digitalSales', 'date'));
    }

    // 2. Weekly Sales Report
    public function weeklySales(Request $request)
    {
        $storeId = Auth::user()->store_id;
        
        // Default to current week (Monday to Sunday)
        $startOfWeek = $request->input('start_date', now()->startOfWeek()->format('Y-m-d'));
        $endOfWeek = $request->input('end_date', now()->endOfWeek()->format('Y-m-d'));

        // Group sales by Date
        $dailyStats = Sale::where('store_id', $storeId)
            ->whereBetween('created_at', [$startOfWeek . ' 00:00:00', $endOfWeek . ' 23:59:59'])
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total_sales, COUNT(*) as total_orders, SUM(subtotal) as total_subtotal, SUM(tax_amount) as total_tax')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        $totalRevenue = $dailyStats->sum('total_sales');
        $totalOrders = $dailyStats->sum('total_orders');

        return view('store.sales.weekly', compact('dailyStats', 'totalRevenue', 'totalOrders', 'startOfWeek', 'endOfWeek'));
    }
    // 1. Add Item to DB Cart
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $storeId = Auth::user()->store_id;
        $user = Auth::user();
        $product = Product::find($request->product_id);

        // Get or Create Active Cart
        $cart = Cart::firstOrCreate(
            [
                'user_id' => $user->id,
                'store_id' => $storeId,
                'status' => 'active'
            ],
            ['total_amount' => 0]
        );

        // Check if item exists in cart
        $cartItem = $cart->items()->where('product_id', $product->id)->first();

        if ($cartItem) {
            $cartItem->quantity += $request->quantity;
            $cartItem->total = $cartItem->quantity * $cartItem->price;
            $cartItem->save();
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'price' => $product->price,
                'total' => $product->price * $request->quantity
            ]);
        }

        $this->recalculateCartTotal($cart);

        return response()->json([
            'success' => true,
            'cart' => $this->getFormattedCart($cart)
        ]);
    }

    // 2. Update Quantity
    public function updateCart(Request $request)
    {
        $cartItem = CartItem::findOrFail($request->item_id);

        if ($request->quantity <= 0) {
            $cartItem->delete();
        } else {
            $cartItem->quantity = $request->quantity;
            $cartItem->total = $cartItem->quantity * $cartItem->price;
            $cartItem->save();
        }

        $this->recalculateCartTotal($cartItem->cart);

        return response()->json([
            'success' => true,
            'cart' => $this->getFormattedCart($cartItem->cart)
        ]);
    }

    // 3. Remove Item
    public function removeCartItem(Request $request)
    {
        $cartItem = CartItem::findOrFail($request->item_id);
        $cart = $cartItem->cart;
        $cartItem->delete();

        $this->recalculateCartTotal($cart);

        return response()->json([
            'success' => true,
            'cart' => $this->getFormattedCart($cart)
        ]);
    }

    // 4. Clear Cart
    public function clearCart()
    {
        $cart = Cart::where('user_id', Auth::id())
            ->where('store_id', Auth::user()->store_id)
            ->where('status', 'active')
            ->first();

        if ($cart) {
            $cart->items()->delete();
            $cart->total_amount = 0;
            $cart->save();
        }

        return response()->json(['success' => true]);
    }

    // Helper: Recalculate Total
    private function recalculateCartTotal($cart)
    {
        $total = $cart->items()->sum('total');
        $cart->update(['total_amount' => $total]);
    }

    // Helper: Format Cart for JS
    private function getFormattedCart($cart)
    {
        return $cart->fresh()->items->map(function ($item) {
            return [
                'item_id' => $item->id, // CartItem ID (for updates)
                'id' => $item->product_id, // Product ID
                'name' => $item->product->product_name,
                'price' => (float)$item->price,
                'quantity' => $item->quantity,
                'max' => $item->product->storeStocks()->where('store_id', Auth::user()->store_id)->sum('quantity'), // Fetch actual stock
            ];
        });
    }
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
                ->orWhereHas('customer', function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%");
                });
        }

        $orders = $query->paginate(10);

        return view('store.sales.orders', compact('orders'));
    }

    // ... existing methods ...

    // Show Single Order Details
    public function showOrder($id)
    {
        $storeId = Auth::user()->store_id;

        // Fetch Sale with Relations (Items, Product, Customer)
        $sale = Sale::where('store_id', $storeId)
            ->with(['items.product', 'customer'])
            ->findOrFail($id);

        return view('store.sales.show', compact('sale'));
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
