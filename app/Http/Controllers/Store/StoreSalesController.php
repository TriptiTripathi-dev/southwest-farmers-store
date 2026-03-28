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
use Illuminate\Support\Facades\Log;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\StoreDetail;
use App\Models\StoreNotification;
use App\Services\PosAgentService;
use Illuminate\Support\Facades\Validator;

class StoreSalesController extends Controller
{
    public function index(Request $request)
    {
        $categories = ProductCategory::select('id', 'name')->distinct()->orderBy('name')->get();
        $storeId = Auth::user()->store_id;

        // Fetch active cart for logged in user
        $currentCart = Cart::where('user_id', Auth::id())
            ->where('store_id', $storeId)
            ->where('status', 'active')
            ->with(['items.product.storeStocks'])
            ->first();

        // Pre-load first 24 products with stock > 0 for instant rendering
        $initialProducts = Product::where('is_active', true)
            ->whereHas('storeStocks', function ($q) use ($storeId) {
                $q->where('store_id', $storeId)->where('quantity', '>', 0);
            })
            ->with(['storeStocks' => function ($q) use ($storeId) {
                $q->where('store_id', $storeId);
            }])
            ->orderBy('product_name', 'asc')
            ->limit(24)
            ->get();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'currentCart' => $currentCart,
                'initialProducts' => $initialProducts,
                'categories' => $categories
            ]);
        }

        $posSettings = \App\Models\QuickPosSetting::first();
        $store = $storeId ? StoreDetail::find($storeId) : null;

        return view('store.sales.pos', compact('categories', 'currentCart', 'initialProducts', 'store', 'posSettings'));
    }

    public function checkoutPage()
    {
        $storeId = Auth::user()->store_id;

        // Fetch active cart
        $currentCart = Cart::where('user_id', Auth::id())
            ->where('store_id', $storeId)
            ->where('status', 'active')
            ->with('items.product')
            ->first();

        if (!$currentCart || $currentCart->items->isEmpty()) {
            return redirect()->route('store.sales.pos')->with('error', 'Cart is empty. Add items before checkout.');
        }

        // Fetch customers for selection
        $customers = StoreCustomer::where('store_id', $storeId)->orderBy('name')->get();

        // Fetch global settings for PAX toggle
        $settings = \App\Models\QuickPosSetting::first();
        $paxEnabled = $settings ? $settings->pax_enabled : false;

        $store = $storeId ? StoreDetail::find($storeId) : null;

        return view('store.sales.checkout', compact('currentCart', 'customers', 'paxEnabled', 'store'));
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
            'sales',
            'totalRevenue',
            'totalTax',
            'totalDiscount',
            'totalOrders',
            'startDate',
            'endDate',
            'paymentMethod'
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

    // 4. Clear Cart / Hold Cart
    public function clearCart(Request $request)
    {
        $cart = Cart::where('user_id', Auth::id())
            ->where('store_id', Auth::user()->store_id)
            ->where('status', 'active')
            ->first();

        if ($cart) {
            if ($request->has('hold') && $request->hold) {
                // If cart is empty, don't hold
                if (!$cart->items()->exists()) {
                    return response()->json(['success' => false, 'message' => 'Cart is empty.']);
                }
                $cart->update(['status' => 'held']);
            } else {
                $cart->items()->delete();
                $cart->total_amount = 0;
                $cart->save();
            }
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

        // If status is held, return held carts instead of sales
        if ($request->status === 'held') {
            $heldCarts = Cart::where('store_id', $storeId)
                ->where('user_id', Auth::id())
                ->where('status', 'held')
                ->withCount('items')
                ->orderBy('updated_at', 'desc')
                ->get();

            return response()->json($heldCarts);
        }

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

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($orders);
        }

        return view('store.sales.orders', compact('orders'));
    }

    /**
     * Restore a held order (Cart) to active status
     */
    public function restoreHeldOrder($id)
    {
        $storeId = Auth::user()->store_id;
        $heldCart = Cart::where('id', $id)
            ->where('store_id', $storeId)
            ->where('status', 'held')
            ->firstOrFail();

        // 1. Clear current active cart (or hold it if preferred, here we just clear it for simplicity)
        $activeCart = Cart::where('user_id', Auth::id())
            ->where('store_id', $storeId)
            ->where('status', 'active')
            ->first();

        DB::transaction(function () use ($heldCart, $activeCart) {
            if ($activeCart) {
                $activeCart->items()->delete();
                $activeCart->delete();
            }

            // 2. Set this held cart to active
            $heldCart->update(['status' => 'active']);
        });

        return response()->json(['success' => true, 'message' => 'Order restored successfully.']);
    }

    /**
     * Delete a held order (Cart)
     */
    public function deleteHeldOrder($id)
    {
        $storeId = Auth::user()->store_id;
        $heldCart = Cart::where('id', $id)
            ->where('store_id', $storeId)
            ->where('status', 'held')
            ->firstOrFail();

        DB::transaction(function () use ($heldCart) {
            $heldCart->items()->delete();
            $heldCart->delete();
        });

        return response()->json(['success' => true, 'message' => 'Held order deleted.']);
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

        $store = $storeId ? StoreDetail::find($storeId) : null;

        return view('store.sales.show', compact('sale', 'store'));
    }
    public function updateOrderStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled'
        ]);

        $storeId = Auth::user()->store_id;
        $sale = Sale::where('store_id', $storeId)->findOrFail($id);

        $sale->update([
            'status' => $request->status
        ]);

        return back()->with('success', 'Order status updated successfully!');
    }

    public function searchProduct(Request $request)
    {
        $term = $request->term;
        $category = $request->category;
        $storeId = Auth::user()->store_id;

        // Base query for active products
        $query = Product::where('products.is_active', true);

        // Filter by Category
        if ($category && $category !== 'all') {
            $query->where('products.category_id', $category);
        }

        // Search Term
        if ($term) {
            $query->where(function ($q) use ($term) {
                $q->where('products.product_name', 'LIKE', "%{$term}%")
                    ->orWhere('products.barcode', 'LIKE', "%{$term}%")
                    ->orWhere('products.upc', 'LIKE', "%{$term}%");
            });
        }

        // Requirement: stock > 0
        // We join or use whereHas to ensure we only get products with stock in THIS store
        $products = $query->whereHas('storeStocks', function ($q) use ($storeId) {
            $q->where('store_id', $storeId)
                ->where('quantity', '>', 0);
        })
            ->with(['storeStocks' => function ($q) use ($storeId) {
                $q->where('store_id', $storeId);
            }])
            ->orderBy('products.product_name', 'asc')
            ->limit(32)
            ->get()
            ->map(function ($p) {
                $stock = $p->storeStocks->first();
                return [
                    'id' => $p->id,
                    'product_name' => $p->product_name,
                    'barcode' => $p->barcode,
                    'upc' => $p->upc,
                    'selling_price' => ($stock && $stock->selling_price > 0) ? $stock->selling_price : $p->price,
                    'quantity' => $stock ? $stock->quantity : 0,
                    'icon' => $p->icon,
                ];
            });

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
    public function checkout(Request $request, StoreStockService $stockService, \App\Services\PosAgentService $posAgentService)
    {
        $request->validate([
            'cart' => 'required|json',
            'customer_id' => 'required|exists:store_customers,id',
            'payment_method' => 'required|in:cash,card,check,upi,bank_transfer',
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

        $paymentMethod = strtolower((string) $request->payment_method);
        $totalAmount = round((float) $request->total_amount, 2);

        // Card authorization guardrails:
        // - Declined transactions are rejected.
        // - Partial approvals are rejected.
        // - Approved amount must exactly match order total.
        if ($paymentMethod === 'card') {
            $request->validate([
                'card_auth_status' => 'required|in:approved,declined,partial',
                'card_approved_amount' => 'required|numeric|min:0',
            ]);

            $cardAuthStatus = strtolower((string) $request->card_auth_status);
            $cardApprovedAmount = round((float) $request->card_approved_amount, 2);

            if ($cardAuthStatus === 'declined') {
                return response()->json([
                    'message' => 'Card declined. Choose another payment method.',
                    'error_code' => 'CARD_DECLINED',
                    'allow_switch_to_cash' => true,
                ], 422);
            }

            if ($cardAuthStatus === 'partial' || $cardApprovedAmount < $totalAmount || abs($cardApprovedAmount - $totalAmount) > 0.009) {
                return response()->json([
                    'message' => 'Insufficient funds. Full amount required.',
                    'error_code' => 'PARTIAL_AUTH_DECLINED',
                    'allow_switch_to_cash' => true,
                ], 422);
            }
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
                'payment_method' => strtoupper($paymentMethod), // Fixed typo: use $paymentMethod and convert to uppercase
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
            StoreNotification::create([
                'user_id' => Auth::id(),
                'store_id' => Auth::user()->store_id,
                'title' => 'New Sale',
                'message' => "Invoice #{$sale->invoice_number} generated for $" . number_format($sale->total_amount, 2),
                'type' => 'success',
                'url' => route('store.sales.orders.show', $sale->id),
            ]);

            // POS Hardware Integration (Strict Response-Driven Flow)
            // Hardware actions decide whether to commit or rollback the database logic based EXACTLY on responses.
            $posWarning = null;
            try {
                $store = \App\Models\StoreDetail::where('id', $storeId)->first();
                $terminalId = $store ? $store->pos_terminal_id : null;

                if ($terminalId) {
                    if ($paymentMethod === 'cash') {
                        // 1. Check Drawer Status
                        $drawerStatus = $posAgentService->getCashDrawerStatus($terminalId);
                        Log::info('POS Checkout: Cash Drawer Status Response', ['response' => $drawerStatus]);

                        // Strict check on 'success' key; 'configured' is preferred but we fallback to success
                        if (!$drawerStatus || empty($drawerStatus['success'])) {
                            DB::rollBack();
                            $errMsg = $drawerStatus['message'] ?? 'Cash Drawer is offline or not configured.';
                            return response()->json([
                                'success' => false,
                                'message' => $errMsg
                            ], 422);
                        }

                        // 2. Open Cash Drawer
                        $opened = $posAgentService->openCashDrawer($terminalId);
                        Log::info('POS Checkout: Open Cash Drawer Response', ['response' => $opened]);

                        if (!$opened || empty($opened['success'])) {
                            DB::rollBack();
                            $errMsg = $opened['message'] ?? 'Failed to open Cash Drawer via Agent.';
                            return response()->json([
                                'success' => false,
                                'message' => $errMsg
                            ], 422);
                        }
                    }

                    // Note: Receipt printing is deferred to the frontend modal which hits `/store/pos/manual-print`
                    // after this checkout responds successfully, matching the requested workflow.
                }
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('POS Hardware Integration Error @ Checkout', ['error' => $e->getMessage()]);
                return response()->json([
                    'success' => false,
                    'message' => 'Hardware Cloud Agent Exception: ' . $e->getMessage()
                ], 422);
            }

            // Commit only if cash drawer succeeded (or if card payment)
            DB::commit();

            return response()->json([
                'success' => true,
                'sale_id' => $sale->id,
                'invoice' => $invoiceNumber,
                'message' => 'Sale completed successfully!' . ($posWarning ? " ($posWarning)" : '')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get Terminal Status
     */
    public function terminalStatus(\App\Services\PosAgentService $posAgentService)
    {
        $store = StoreDetail::where('id', Auth::user()->store_id)->first();
        $terminalId = $store ? $store->pos_terminal_id : null;

        if (!$terminalId) {
            return response()->json(['status' => 'offline', 'message' => 'Terminal ID not configured.']);
        }

        try {
            $raw = $posAgentService->getTerminalStatus($terminalId);

            $isOnline = false;
            if (is_array($raw)) {
                if (!empty($raw['success']) && !empty($raw['registered'])) $isOnline = true;
                if (isset($raw['status']) && strtolower($raw['status']) === 'approved') $isOnline = true;
                if (!empty($raw['approved'])) $isOnline = true;
            }

            return response()->json([
                'status'      => $isOnline ? 'Approved' : 'offline',
                'online'      => $isOnline,
                'raw'         => $raw,
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'offline', 'online' => false]);
        }
    }

    /**
     * Proxy to get printer list from the agent.
     */
    public function getPrinters(\App\Services\PosAgentService $posAgentService)
    {
        $store = \App\Models\StoreDetail::where('id', Auth::user()->store_id)->first();
        $terminalId = $store ? $store->pos_terminal_id : null;

        if (!$terminalId) {
            return response()->json(['success' => false, 'message' => 'Terminal ID not configured.']);
        }

        $result = $posAgentService->getPrinterList($terminalId);
        return response()->json($result);
    }

    /**
     * Get weight from scale
     */
    public function getWeight(\App\Services\PosAgentService $posAgentService)
    {
        $store = \App\Models\StoreDetail::where('id', Auth::user()->store_id)->first();
        $terminalId = $store ? $store->pos_terminal_id : null;

        if (!$terminalId) {
            return response()->json(['success' => false, 'message' => 'Terminal ID not configured.']);
        }

        $response = $posAgentService->getWeight($terminalId);
        return response()->json($response ?: ['success' => false, 'weight' => null]);
    }

    /**
     * Get last scan from scanner
     */
    public function getLastScan(\App\Services\PosAgentService $posAgentService)
    {
        $store = \App\Models\StoreDetail::where('id', Auth::user()->store_id)->first();
        $terminalId = $store ? $store->pos_terminal_id : null;

        if (!$terminalId) {
            return response()->json(['success' => false, 'message' => 'Terminal ID not configured.']);
        }

        $response = $posAgentService->getLastScan($terminalId);

        if (isset($response['scan']['value'])) {
            // Map value to barcode to match existing frontend expectation
            $response['scan']['barcode'] = $response['scan']['value'];
        }

        return response()->json($response ?: ['success' => false]);
    }

    /**
     * Manual Print — triggered by "Print Receipt" button click in the success modal.
     * Sends sale data to the hardware printer API; no browser print dialog.
     */
    public function manualPrint(Request $request, \App\Services\PosAgentService $posAgentService)
    {
        $invoiceNumber = $request->input('invoice_number');
        if (!$invoiceNumber) {
            return response()->json(['success' => false, 'message' => 'Invoice number required.']);
        }

        $store = \App\Models\StoreDetail::where('id', Auth::user()->store_id)->first();
        $terminalId = $store ? $store->pos_terminal_id : null;

        if (!$terminalId) {
            return response()->json(['success' => false, 'message' => 'Terminal ID not configured.']);
        }

        $sale = \App\Models\Sale::with('items.product')
            ->where('invoice_number', $invoiceNumber)
            ->where('store_id', $store->id)
            ->first();

        if (!$sale) {
            return response()->json(['success' => false, 'message' => 'Sale not found.']);
        }

        $result = $posAgentService->printReceipt($terminalId, $sale, $request->input('printer_name'));
        return response()->json($result);
    }
    /**
     * Check PAX terminal status for current store
     */
    public function checkPaxStatus(PosAgentService $posAgentService)
    {
        $store = \App\Models\StoreDetail::where('id', Auth::user()->store_id)->first();
        if (!$store || !$store->pos_terminal_id) {
            return response()->json(['success' => false, 'message' => 'Terminal ID not configured.'], 422);
        }

        $status = $posAgentService->getPaymentStatus($store->pos_terminal_id);
        return response()->json($status);
    }

    /**
     * Initiate PAX payment
     */
    public function initiatePaxPayment(Request $request, PosAgentService $posAgentService)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0.01',
            'order_id' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $store = \App\Models\StoreDetail::where('id', Auth::user()->store_id)->first();
        if (!$store || !$store->pos_terminal_id) {
            return response()->json(['success' => false, 'message' => 'Terminal ID not configured.'], 422);
        }

        $result = $posAgentService->initiatePayment(
            $store->pos_terminal_id,
            $request->amount,
            $request->order_id
        );

        return response()->json($result);
    }

    /**
     * Cancel PAX payment
     */
    public function cancelPaxPayment(PosAgentService $posAgentService)
    {
        $store = \App\Models\StoreDetail::where('id', Auth::user()->store_id)->first();
        if (!$store || !$store->pos_terminal_id) {
            return response()->json(['success' => false, 'message' => 'Terminal ID not configured.'], 422);
        }

        $result = $posAgentService->cancelPayment($store->pos_terminal_id);
        return response()->json($result);
    }
}
