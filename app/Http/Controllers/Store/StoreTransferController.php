<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StockTransfer;
use App\Models\StoreStock;
use App\Models\ProductBatch;
use App\Models\StockTransaction;
use App\Services\StoreStockService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StoreTransferController extends Controller
{
    // List Transfers (Inbox/Outbox)
    public function index()
    {
        $storeId = Auth::user()->store_id;
        
        // Requests I made (Incoming)
        $incoming = StockTransfer::where('to_store_id', $storeId)->with(['product', 'fromStore'])->latest()->get();
        
        // Requests I received (Outgoing - I need to send)
        $outgoing = StockTransfer::where('from_store_id', $storeId)->with(['product', 'toStore'])->latest()->get();

        return view('store.transfers.index', compact('incoming', 'outgoing'));
    }

    // Create New Request
    public function store(Request $request)
    {
        $request->validate([
            'from_store_id' => 'required',
            'product_id' => 'required',
            'quantity' => 'required|integer|min:1'
        ]);

        StockTransfer::create([
            'to_store_id' => Auth::user()->store_id, // Me (Requester)
            'from_store_id' => $request->from_store_id, // Sender
            'product_id' => $request->product_id,
            'quantity_requested' => $request->quantity,
            'status' => 'pending'
        ]);

        return back()->with('success', 'Transfer Request Sent!');
    }

    // Approve & Dispatch (Sender Side) - Uses FIFO Logic
    public function dispatchTransfer(Request $request, StockTransfer $transfer, StoreStockService $stockService)
    {
        if($transfer->from_store_id != Auth::user()->store_id) abort(403);
        
        try {
            DB::beginTransaction();

            // 1. Deduct Stock from My Store (Sender) using FIFO
            $stockService->deductStockFIFO(
                Auth::user()->store_id,
                $transfer->product_id,
                $transfer->quantity_requested,
                "Transfer to Store #" . $transfer->to_store_id,
                Auth::id()
            );

            // 2. Update Transfer Status
            $transfer->update([
                'status' => 'dispatched',
                'quantity_sent' => $transfer->quantity_requested
            ]);
            
            // Note: Stock is now "In Transit" (Deducted from A, not yet added to B)
            
            DB::commit();
            return back()->with('success', 'Stock Dispatched Successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // Receive Stock (Requester Side)
    public function receiveTransfer(Request $request, StockTransfer $transfer)
    {
        if($transfer->to_store_id != Auth::user()->store_id) abort(403);
        if($transfer->status != 'dispatched') abort(400);

        // 1. Add Stock to My Store
        // Ideally, we should add to a specific batch or create a new batch. 
        // For simplicity, we add to general stock here.
        
        $stock = StoreStock::firstOrCreate(
            ['store_id' => Auth::user()->store_id, 'product_id' => $transfer->product_id],
            ['quantity' => 0]
        );
        
        $stock->increment('quantity', $transfer->quantity_sent);

        // 2. Close Transfer
        $transfer->update([
            'status' => 'received',
            'quantity_received' => $transfer->quantity_sent
        ]);

        StockTransaction::create([
            'store_id' => Auth::user()->store_id,
            'product_id' => $transfer->product_id,
            'type' => 'transfer_in',
            'quantity_change' => $transfer->quantity_sent,
            'remarks' => 'Received from Store #' . $transfer->from_store_id,
            'ware_user_id' => Auth::id()
        ]);

        return back()->with('success', 'Stock Received & Added to Inventory');
    }
}