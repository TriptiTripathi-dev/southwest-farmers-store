<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\StoreStock;
use App\Models\StockRequest;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StockRequestImport;
use Illuminate\Support\Facades\Auth;
use App\Models\StockAdjustment;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class StoreInventoryController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = StoreStock::where('store_id', $user->store_id)
            ->with('product');
            
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('product', function($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $stocks = $query->latest()->paginate(15);
        return view('inventory.index', compact('stocks'));
    }

    public function requestStock(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        $user = Auth::user();

        StockRequest::create([
            'store_id'           => $user->store_id,
            'product_id'         => $request->product_id,
            'requested_quantity' => $request->quantity,
            'status'             => 'pending',
        ]);

        return back()->with('success', 'Stock requisition sent to Warehouse successfully!');
    }

    // --- Bulk Import Methods ---
    public function downloadSampleCsv()
    {
        return response()->streamDownload(function () {
            echo "sku,quantity\n";
            echo "STR-001,10\n";
            echo "STR-002,5\n";
        }, 'stock_request_sample.csv');
    }

    public function importStockRequests(Request $request)
    {
        $request->validate(['file' => 'required|mimes:csv,txt,xlsx']);
        Excel::import(new StockRequestImport, $request->file('file'));
        return back()->with('success', 'Stock requests imported successfully.');
    }

    // --- UPDATED: Requests Listing with Tabs ---
    public function requests(Request $request)
    {
        $user = Auth::user();
        $storeId = $user->store_id ?? $user->id;
        $status = $request->get('status', 'pending');
        $search = $request->input('search');

        $query = StockRequest::where('store_id', $storeId)->with('product');

        // Search Filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('product', fn($q) => $q->where('product_name', 'like', "%{$search}%")->orWhere('sku', 'like', "%{$search}%"));
            });
        }

        // Tab Status Logic
        if ($status === 'history') {
            $query->whereIn('status', [StockRequest::STATUS_COMPLETED, StockRequest::STATUS_REJECTED]);
        } elseif ($status === 'in_transit') {
            $query->where('status', StockRequest::STATUS_DISPATCHED);
        } else {
            $query->where('status', $status);
        }

        $requests = $query->latest()->paginate(15)->appends($request->query());

        // Count Stats for Tabs
        $pendingCount = StockRequest::where('store_id', $storeId)->where('status', 'pending')->count();
        $inTransitCount = StockRequest::where('store_id', $storeId)->where('status', 'dispatched')->count();
        $completedCount = StockRequest::where('store_id', $storeId)->where('status', 'completed')->count();
        $rejectedCount = StockRequest::where('store_id', $storeId)->where('status', 'rejected')->count();

        // Products for Dropdown
        $products = Product::where('is_active', true)
            ->select('id', 'product_name', 'sku', 'unit')
            ->orderBy('product_name')
            ->get();

        return view('inventory.requests', compact(
            'requests', 'products', 
            'pendingCount', 'inTransitCount', 'completedCount', 'rejectedCount'
        ));
    }

    // --- NEW: Show Request Details ---
    public function showRequest($id)
    {
        $user = Auth::user();
        $storeId = $user->store_id ?? $user->id;

        $stockRequest = StockRequest::where('store_id', $storeId)
            ->with(['product', 'store'])
            ->findOrFail($id);

        return view('inventory.show', compact('stockRequest'));
    }

    // --- NEW: Upload Payment Proof (Store Action) ---
    public function uploadPaymentProof(Request $request)
    {
        $request->validate([
            'request_id' => 'required|exists:stock_requests,id',
            'store_payment_proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'store_remarks' => 'required|string'
        ]);

        $user = Auth::user();
        $stockRequest = StockRequest::where('id', $request->request_id)
            ->where('store_id', $user->store_id ?? $user->id)
            ->firstOrFail();

        // Prevent update if already completed (optional check)
        if ($stockRequest->status === StockRequest::STATUS_COMPLETED) {
            return response()->json(['success' => false, 'message' => 'Request already completed.'], 400);
        }

        $path = $request->file('store_payment_proof')->store('payment_proofs', 'public');

        $stockRequest->update([
            'store_payment_proof' => $path,
            'store_remarks' => $request->store_remarks
        ]);

        return response()->json(['success' => true, 'message' => 'Payment proof uploaded successfully!']);
    }

    // Cancel Pending Request
    public function cancelRequest($id)
    {
        $user = Auth::user();
        $stockRequest = StockRequest::where('id', $id)
            ->where('store_id', $user->store_id ?? $user->id)
            ->firstOrFail();

        if ($stockRequest->status == 'pending') {
            $stockRequest->delete();
            return back()->with('success', 'Stock request cancelled successfully.');
        }

        return back()->with('error', 'Cannot cancel a processed request.');
    }

    // ... (Adjustment methods remain unchanged) ...
    public function adjustments()
    {
        // ... existing code ...
        $user = Auth::user();
        $storeId = $user->store_id ?? $user->id;
        $adjustments = StockAdjustment::where('store_id', $storeId)->with(['product', 'user'])->latest()->paginate(15);
        $products = Product::where('is_active', true)->select('id', 'product_name', 'sku', 'unit')->get();
        return view('inventory.adjustments', compact('adjustments', 'products'));
    }

    public function storeAdjustment(Request $request)
    {
        // ... existing code ...
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
            'operation'  => 'required|in:add,subtract',
            'reason'     => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        $storeId = $user->store_id ?? $user->id;

        DB::transaction(function () use ($request, $user, $storeId) {
            $stock = StoreStock::firstOrNew(['store_id' => $storeId, 'product_id' => $request->product_id]);
            if ($request->operation === 'add') {
                $stock->quantity = ($stock->quantity ?? 0) + $request->quantity;
            } else {
                if (($stock->quantity ?? 0) < $request->quantity) {
                    throw new \Exception("Insufficient stock.");
                }
                $stock->quantity -= $request->quantity;
            }
            $stock->save();

            StockAdjustment::create([
                'store_id' => $storeId, 'product_id' => $request->product_id, 'user_id' => $user->id,
                'quantity' => $request->quantity, 'operation' => $request->operation, 'reason' => $request->reason,
            ]);
        });

        return back()->with('success', 'Stock adjusted successfully.');
    }
}