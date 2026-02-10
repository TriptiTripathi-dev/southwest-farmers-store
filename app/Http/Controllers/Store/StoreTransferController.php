<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StockTransfer;
use App\Models\StoreStock;
use App\Models\StoreDetail;
use App\Models\Product; // Added
use App\Models\StockTransaction;
use App\Services\StoreStockService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StoreTransferController extends Controller
{
    // List Transfers
    public function index()
    {
        $storeId = Auth::user()->store_id;
        
        // Incoming Requests (I am the Requester)
        $incoming = StockTransfer::where('to_store_id', $storeId)
            ->with(['product', 'fromStore'])
            ->latest()
            ->get();
        
        // Outgoing Requests (I am the Sender/Fulfiller)
        $outgoing = StockTransfer::where('from_store_id', $storeId)
            ->with(['product', 'toStore'])
            ->latest()
            ->get();

        // Get all other stores for the dropdown (excluding my store)
        $stores = StoreDetail::where('id', '!=', $storeId)->get();
        // Get products for dropdown
        $products = Product::select('id', 'product_name', 'sku')->get();

        return view('store.transfers.index', compact('incoming', 'outgoing', 'stores', 'products'));
    }

    // 1. Create Request
    public function store(Request $request)
    {
        $request->validate([
            'from_store_id' => 'required|exists:store_details,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        // Generate a Transfer Number
        $transferNumber = 'TRF-' . strtoupper(Str::random(8));

        StockTransfer::create([
            'transfer_number' => $transferNumber,
            'to_store_id' => Auth::user()->store_id,   // Me (Requester)
            'from_store_id' => $request->from_store_id, // Sender
            'product_id' => $request->product_id,
            'quantity_sent' => $request->quantity, // Using 'sent' as 'requested' initially for Pending state
            'quantity_received' => 0,
            'status' => 'pending',
            'created_by' => Auth::id()
        ]);

        return back()->with('success', 'Transfer Request Sent!');
    }

    // 2. Dispatch (Sender Side)
    public function dispatchTransfer(Request $request, $id, StoreStockService $stockService)
    {
        $transfer = StockTransfer::findOrFail($id);

        if($transfer->from_store_id != Auth::user()->store_id) abort(403, 'Unauthorized');
        if($transfer->status != 'pending') abort(400, 'Transfer already processed');
        
        try {
            DB::beginTransaction();

            // Check if we have enough stock
            $currentStock = StoreStock::where('store_id', $transfer->from_store_id)
                ->where('product_id', $transfer->product_id)
                ->value('quantity');

            if (!$currentStock || $currentStock < $transfer->quantity_sent) {
                return back()->with('error', 'Insufficient Stock to fulfill request.');
            }

            // Deduct Stock from My Store (Sender)
            $stockService->deductStockFIFO(
                Auth::user()->store_id,
                $transfer->product_id,
                $transfer->quantity_sent,
                "Transfer Out: " . $transfer->transfer_number,
                Auth::id()
            );

            // Update Transfer Status
            $transfer->update([
                'status' => 'dispatched',
                'approved_by' => Auth::id()
            ]);
            
            DB::commit();
            return back()->with('success', 'Stock Dispatched Successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // 3. Receive & Handle Discrepancy (Requester Side)
    public function receiveTransfer(Request $request, $id)
    {
        $transfer = StockTransfer::findOrFail($id);

        if($transfer->to_store_id != Auth::user()->store_id) abort(403);
        if($transfer->status != 'dispatched') abort(400);

        $request->validate([
            'received_qty' => 'required|integer|min:0|max:' . $transfer->quantity_sent
        ]);

        try {
            DB::beginTransaction();

            $receivedQty = $request->received_qty;
            $sentQty = $transfer->quantity_sent;
            $status = 'completed';
            $remarks = 'Received in full.';

            // Discrepancy Check
            if ($receivedQty < $sentQty) {
                $diff = $sentQty - $receivedQty;
                $remarks = "Discrepancy: Sent $sentQty, Received $receivedQty. Missing $diff.";
                // In a real scenario, you might trigger a 'Dispute' status here
            }

            // Add Stock to My Store (Receiver)
            $stock = StoreStock::firstOrCreate(
                ['store_id' => Auth::user()->store_id, 'product_id' => $transfer->product_id],
                ['quantity' => 0, 'selling_price' => 0, 'min_stock' => 0, 'max_stock' => 0]
            );
            
            $stock->increment('quantity', $receivedQty);

            // Close Transfer
            $transfer->update([
                'status' => $status,
                'quantity_received' => $receivedQty,
                'received_by' => Auth::id(),
                'remarks' => $remarks
            ]);

            // Log Transaction
            StockTransaction::create([
                'store_id' => Auth::user()->store_id,
                'product_id' => $transfer->product_id,
                'type' => 'transfer_in',
                'quantity_change' => $receivedQty,
                'running_balance' => $stock->quantity,
                'reference_id' => $transfer->transfer_number,
                'remarks' => "Transfer In from Store #" . $transfer->from_store_id,
                'ware_user_id' => null // Since it's a store user
            ]);

            DB::commit();
            return back()->with('success', 'Stock Received. ' . $remarks);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}