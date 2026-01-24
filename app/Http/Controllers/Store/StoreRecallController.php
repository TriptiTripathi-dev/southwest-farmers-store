<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\RecallRequest;
use App\Models\StoreStock;
use App\Models\StockTransaction;
use App\Models\StoreDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StoreRecallController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $storeId = $user->store_id;

        $recalls = RecallRequest::where('store_id', $storeId)
            ->with(['product', 'initiator'])
            ->latest()
            ->paginate(15);

        return view('store.stock-control.recall.index', compact('recalls'));
    }

    public function create()
    {
        $user = Auth::user();
        $storeId = $user->store_id;

        $products = StoreStock::where('store_id', $storeId)
            ->where('quantity', '>', 0)
            ->with('product')
            ->get();

        return view('store.stock-control.recall.create', compact('products'));
    }

    // --- STEP 1: Store Requests Recall ---
    public function store(Request $request)
    {
        $user = Auth::user();
        $storeId = $user->store_id;

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string',
            'reason_remarks' => 'nullable|string'
        ]);

        // Check Physical Stock
        $stock = StoreStock::where('store_id', $storeId)
            ->where('product_id', $request->product_id)
            ->first();

        if (!$stock || $stock->quantity < $request->quantity) {
            return back()->with('error', 'Insufficient stock in store.');
        }

        RecallRequest::create([
            'store_id' => $storeId,
            'product_id' => $request->product_id,
            'requested_quantity' => $request->quantity,
            'reason' => $request->reason,
            'reason_remarks' => $request->reason_remarks,
            // UPDATED STATUS: Waiting for Warehouse
            'status' => 'pending_warehouse_approval',
            'initiated_by' => $user->id,
        ]);

        return redirect()->route('store.stock-control.recall.index')
            ->with('success', 'Request sent to Warehouse. Please wait for approval.');
    }

    public function show($id)
    {
        $user = Auth::user();
        $storeId = $user->store_id;
        $recall = RecallRequest::with('product')->where('id', $id)
            ->where('store_id', $storeId)
            ->firstOrFail();

        return view('store.stock-control.recall.show', compact('recall'));
    }

    // --- STEP 3: Store Dispatches (After Approval) ---
    public function dispatch(Request $request, $id)
    {
        $user = Auth::user();
        $storeId = $user->store_id;
        $recall = RecallRequest::where('id', $id)->firstOrFail();
        if ($recall->store_id != $storeId) abort(403);

        // Security Check: Only allow dispatch if Warehouse has approved
        if ($recall->status !== 'approved') {
            return back()->with('error', 'This request is not approved yet.');
        }

        $request->validate([
            'dispatch_qty' => 'required|integer|min:1|max:' . $recall->requested_quantity,
        ]);

        DB::transaction(function () use ($request, $recall, $user, $storeId) {

            // 1. Deduct Store Stock
            $storeStock = StoreStock::where('store_id', $storeId)
                ->where('product_id', $recall->product_id)
                ->first();

            if (!$storeStock || $storeStock->quantity < $request->dispatch_qty) {
                throw new \Exception("Not enough physical stock to dispatch.");
            }

            $storeStock->decrement('quantity', $request->dispatch_qty);
            $wareId = StoreDetail::where('id', $recall->store_id)->first();

            // 2. Log Transaction
            StockTransaction::create([
                'store_id' => $storeId,
                'product_id' => $recall->product_id,
                'type' => 'recall_out',
                'quantity_change' => - ($request->dispatch_qty),
                'running_balance' => $storeStock->quantity,
                'reference_id' => 'RECALL-' . $recall->id,
                'remarks' => 'Dispatched to Warehouse (Approved)',
                'warehouse_id' => $wareId->warehouse_id,
            ]);

            // 3. Update Status
            $recall->update([
                'dispatched_quantity' => $request->dispatch_qty,
                'status' => 'dispatched',
            ]);
        });

        return back()->with('success', 'Stock dispatched successfully.');
    }
}
