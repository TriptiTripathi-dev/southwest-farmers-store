<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\RecallRequest;
use App\Models\ProductBatch;
use App\Models\StoreStock;
use App\Models\StockTransaction;
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

    public function show(RecallRequest $recall)
    {
        $user = Auth::user();
        $storeId = $user->store_id;

        if ($recall->store_id != $storeId) {
            abort(403);
        }

        $recall->load(['product', 'initiator']);

        // Use correct column (change 'quantity' to your actual column if different)
        $batches = ProductBatch::where('product_id', $recall->product_id)
            ->where('warehouse_id', 1)
            ->where('quantity', '>', 0)
            ->orderBy('expiry_date', 'asc')
            ->get();

        return view('store.stock-control.recall.show', compact('recall', 'batches'));
    }

    public function approve(Request $request, RecallRequest $recall)
    {
        $user = Auth::user();
        $storeId = $user->store_id;

        if ($recall->store_id != $storeId || $recall->status != RecallRequest::STATUS_PENDING_STORE_APPROVAL) {
            abort(403);
        }

        $request->validate([
            'approved_quantity' => 'required|integer|min:1|max:' . $recall->requested_quantity,
            'store_remarks' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $recall, $user) {
            $recall->update([
                'approved_quantity' => $request->approved_quantity,
                'store_remarks' => $request->store_remarks,
                'approved_by_store_user_id' => $user->id,
                'status' => $recall->requested_quantity == $request->approved_quantity
                    ? RecallRequest::STATUS_APPROVED_BY_STORE
                    : RecallRequest::STATUS_PARTIAL_APPROVED,
            ]);
        });

        return back()->with('success', 'Recall approved successfully');
    }

    public function reject(Request $request, RecallRequest $recall)
    {
        $user = Auth::user();
        $storeId = $user->store_id;

        if ($recall->store_id != $storeId || $recall->status != RecallRequest::STATUS_PENDING_STORE_APPROVAL) {
            abort(403);
        }

        $request->validate(['store_remarks' => 'required|string']);

        $recall->update([
            'store_remarks' => $request->store_remarks,
            'approved_by_store_user_id' => $user->id,
            'status' => RecallRequest::STATUS_REJECTED_BY_STORE,
        ]);

        return back()->with('success', 'Recall rejected');
    }

    public function dispatch(Request $request, RecallRequest $recall)
    {
        $user = Auth::user();
        $storeId = $user->store_id;

        if ($recall->store_id != $storeId || !in_array($recall->status, [RecallRequest::STATUS_APPROVED_BY_STORE, RecallRequest::STATUS_PARTIAL_APPROVED])) {
            abort(403);
        }

        $request->validate([
            'batches' => 'required|array',
            'batches.*.batch_id' => 'required|exists:product_batches,id',
            'batches.*.quantity' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request, $recall, $user, $storeId) {
            $totalDispatched = 0;

            foreach ($request->batches as $batchData) {
                $batch = ProductBatch::find($batchData['batch_id']);
                if ($batch->remaining_quantity < $batchData['quantity']) {
                    throw new \Exception("Insufficient quantity in batch");
                }

                $batch->decrement('remaining_quantity', $batchData['quantity']);
                $totalDispatched += $batchData['quantity'];

                StockTransaction::create([
                    'product_id' => $recall->product_id,
                    'product_batch_id' => $batch->id,
                    'store_id' => $storeId,
                    'type' => 'recall_out',
                    'quantity_change' => -$batchData['quantity'],
                    'running_balance' => StoreStock::where('store_id', $storeId)->where('product_id', $recall->product_id)->first()->quantity,
                    'reference_id' => 'RECALL-' . $recall->id,
                    'remarks' => 'Store dispatched for recall',
                ]);
            }

            StoreStock::where('store_id', $storeId)
                ->where('product_id', $recall->product_id)
                ->decrement('quantity', $totalDispatched);

            $recall->update([
                'dispatched_quantity' => $totalDispatched,
                'status' => RecallRequest::STATUS_DISPATCHED,
            ]);
        });

        return back()->with('success', 'Stock dispatched for recall');
    }
}
