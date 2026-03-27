<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Cart;
use App\Models\Product;
use App\Models\StockTransaction;
use App\Models\StoreStock;
use App\Models\StoreNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * List customer's orders.
     */
    public function index()
    {
        $orders = Sale::where('customer_id', auth('customer')->id())
            ->latest()
            ->paginate(10);

        return view('website.customer.orders.index', compact('orders'));
    }

    /**
     * Show a specific order.
     */
    public function show($id)
    {
        $order = Sale::where('customer_id', auth('customer')->id())
            ->with('items.product')
            ->findOrFail($id);

        return view('website.customer.orders.show', compact('order'));
    }

    /**
     * Process checkout and create a Sale.
     */
    public function store(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:card,cod'
        ]);

        $user = auth('customer')->user();
        $cart = Cart::where('user_id', $user->id)
            ->where('status', 'active')
            ->with('items.product')
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Your cart is empty'], 400);
        }

        $storeId = $user->store_id ?? 1;

        DB::beginTransaction();
        try {
            // Generate Invoice Number
            $invoicePrefix = 'WEB-' . date('Ymd');
            $lastSale = Sale::whereDate('created_at', today())
                ->orderBy('id', 'desc')
                ->first();
            $seq = $lastSale ? (int)substr($lastSale->invoice_number ?? 'WEB-0000-0000', -4) + 1 : 1;
            $invoiceNumber = $invoicePrefix . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);

            // Create Sale (Order)
            $sale = Sale::create([
                'store_id' => $storeId,
                'customer_id' => $user->id,
                'invoice_number' => $invoiceNumber,
                'subtotal' => $cart->items->sum('total'),
                'tax_amount' => 0,
                'discount_amount' => $cart->discount_amount ?? 0,
                'total_amount' => $cart->total_amount,
                'payment_method' => $request->payment_method,
                'status' => 'pending',   // Initial status for website orders
                'source' => 'website',   // Distinguish from POS sales
                'created_by' => null,    // Created by customer
            ]);

            foreach ($cart->items as $item) {
                // Create SaleItem
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'total' => $item->total,
                ]);

                // Deduct from StoreStock (If stock tracking is enabled for website)
                $storeStock = StoreStock::where('store_id', $storeId)
                    ->where('product_id', $item->product_id)
                    ->first();

                if ($storeStock) {
                    if ($storeStock->quantity < $item->quantity) {
                        throw new \Exception('Insufficient stock for ' . $item->product->product_name);
                    }
                    $storeStock->decrement('quantity', $item->quantity);

                    // Create Stock Transaction
                    StockTransaction::create([
                        'product_id' => $item->product_id,
                        'store_id' => $storeId,
                        'customer_id' => $user->id,
                        'type' => 'sale',
                        'quantity_change' => -$item->quantity,
                        'running_balance' => $storeStock->quantity,
                        'reference_id' => $sale->id,
                        'remarks' => 'Website Order: ' . $invoiceNumber,
                    ]);
                }
            }

            // Create Notification for Store
            StoreNotification::create([
                'store_id' => $storeId,
                'title' => 'New Website Order',
                'message' => "New order #{$invoiceNumber} received from {$user->name}",
                'type' => 'info',
                'url' => route('store.sales.orders.show', $sale->id),
            ]);

            // Clear Cart
            $cart->items()->delete();
            $cart->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully!',
                'redirect' => route('website.checkout.success', $invoiceNumber)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Show success page.
     */
    public function success($invoice)
    {
        $order = Sale::where('invoice_number', $invoice)
            ->where('customer_id', auth('customer')->id())
            ->firstOrFail();

        return view('website.checkout.success', compact('order'));
    }
}
