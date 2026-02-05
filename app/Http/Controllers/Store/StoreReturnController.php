<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\SaleReturn;
use App\Models\SaleReturnItem;
use App\Models\StoreStock;
use App\Models\StockTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StoreReturnController extends Controller
{
    public function index()
    {
        $returns = SaleReturn::where('store_id', Auth::user()->store_id)
            ->with(['sale', 'customer'])
            ->latest()
            ->paginate(10);
        return view('store.sales.returns.index', compact('returns'));
    }

    public function create(Request $request)
    {
        $invoice = $request->get('invoice');
        return view('store.sales.returns.create', compact('invoice'));
    }

    public function searchInvoice(Request $request)
    {
        $sale = Sale::where('invoice_number', $request->invoice)
            ->where('store_id', Auth::user()->store_id)
            ->with(['items.product', 'customer'])
            ->first();

        if (!$sale) return response()->json(['status' => false, 'message' => 'Invoice not found']);

        // Calculate Tax Rate from Sale Data (Tax / Subtotal)
        // Guard against division by zero
        $taxRate = $sale->subtotal > 0 ? ($sale->tax_amount / $sale->subtotal) : 0;

        // Append tax info to response
        $sale->tax_rate = $taxRate;

        return response()->json(['status' => true, 'sale' => $sale]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'items' => 'required|array',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $storeId = Auth::user()->store_id;
            $sale = Sale::with('items')->findOrFail($request->sale_id);

            // Calculate Effective Tax Rate
            $taxRate = $sale->subtotal > 0 ? ($sale->tax_amount / $sale->subtotal) : 0;

            $returnNo = 'RET-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
            $calculatedTotalRefund = 0;
            $itemsToProcess = [];

            // 1. Prepare Data
            foreach ($request->items as $itemData) {
                $originalItem = $sale->items->where('product_id', $itemData['product_id'])->first();

                if (!$originalItem) continue;

                $unitPrice = $originalItem->price;
                $taxPerUnit = $unitPrice * $taxRate;
                $refundUnitPrice = $unitPrice + $taxPerUnit;

                $lineTotal = $refundUnitPrice * $itemData['qty'];
                $calculatedTotalRefund += $lineTotal;

                $itemsToProcess[] = [
                    'product_id' => $itemData['product_id'],
                    'qty' => $itemData['qty'],
                    'refund_unit_price' => $refundUnitPrice
                ];
            }

            // 2. Create Return Record
            $return = SaleReturn::create([
                'store_id' => $storeId,
                'sale_id' => $sale->id,
                'customer_id' => $request->customer_id,
                'return_no' => $returnNo,
                'total_refund' => $calculatedTotalRefund,
                'reason' => $request->reason,
                'created_by' => Auth::id()
            ]);

            // 3. Process Items & Inventory
            foreach ($itemsToProcess as $item) {
                // A. Save Return Item
                SaleReturnItem::create([
                    'sale_return_id' => $return->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['qty'],
                    'refund_price' => $item['refund_unit_price'],
                ]);

                // B. Restore Stock & Calculate New Running Balance
                $stock = StoreStock::where('store_id', $storeId)
                    ->where('product_id', $item['product_id'])
                    ->first();

                if ($stock) {
                    $stock->increment('quantity', $item['qty']);
                    $newRunningBalance = $stock->quantity; // Capture new value
                } else {
                    // Create stock entry if it doesn't exist (edge case)
                    $stock = StoreStock::create([
                        'store_id' => $storeId,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['qty'],
                        'selling_price' => 0
                    ]);
                    $newRunningBalance = $item['qty'];
                }

                // C. Log Transaction with Running Balance
                StockTransaction::create([
                    'store_id' => $storeId,
                    'product_id' => $item['product_id'],
                    'quantity_change' => $item['qty'],
                    'type' => 'return',
                    'running_balance' => $newRunningBalance, // <--- FIXED: Now populated
                    'remarks' => "Return: $returnNo",
                    'ware_user_id' => Auth::id()
                ]);
            }

            DB::commit();
            return redirect()->route('store.sales.returns.index')
                ->with('success', 'Return processed successfully. Refund: $' . number_format($calculatedTotalRefund, 2));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
