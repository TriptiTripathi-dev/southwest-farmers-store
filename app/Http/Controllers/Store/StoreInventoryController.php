<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\StoreStock;
use App\Models\ProductCategory;
use App\Models\StockRequest;
use App\Models\ProductBatch;
use App\Models\StockTransaction;
use App\Models\StockAdjustment;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StockRequestImport;

class StoreInventoryController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = StoreStock::where('store_id', $user->store_id)
            ->with('product');
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('product', function ($q) use ($search) {
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
            'quantity' => 'required|integer|min:1',
        ]);
        $user = Auth::user();
        StockRequest::create([
            'store_id' => $user->store_id,
            'product_id' => $request->product_id,
            'requested_quantity' => $request->quantity,
            'status' => 'pending',
        ]);
        return back()->with('success', 'Stock requisition sent to Warehouse successfully!');
    }

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

    public function requests(Request $request)
    {
        $user = Auth::user();
        $storeId = $user->store_id ?? $user->id;
        $status = $request->get('status', 'pending');
        $search = $request->input('search');
        $query = StockRequest::where('store_id', $storeId)->with('product');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhereHas('product', fn($q) => $q->where('product_name', 'like', "%{$search}%")->orWhere('sku', 'like', "%{$search}%"));
            });
        }
        if ($status === 'history') {
            $query->whereIn('status', [StockRequest::STATUS_COMPLETED, StockRequest::STATUS_REJECTED]);
        } elseif ($status === 'in_transit') {
            $query->where('status', StockRequest::STATUS_DISPATCHED);
        } else {
            $query->where('status', $status);
        }
        $requests = $query->latest()->paginate(15)->appends($request->query());
        $pendingCount = StockRequest::where('store_id', $storeId)->where('status', 'pending')->count();
        $inTransitCount = StockRequest::where('store_id', $storeId)->where('status', 'dispatched')->count();
        $completedCount = StockRequest::where('store_id', $storeId)->where('status', 'completed')->count();
        $rejectedCount = StockRequest::where('store_id', $storeId)->where('status', 'rejected')->count();
        $products = Product::where('is_active', true)
            ->select('id', 'product_name', 'sku', 'unit')
            ->orderBy('product_name')
            ->get();
        return view('inventory.requests', compact(
            'requests',
            'products',
            'pendingCount',
            'inTransitCount',
            'completedCount',
            'rejectedCount'
        ));
    }

    public function showRequest($id)
    {
        $user = Auth::user();
        $storeId = $user->store_id ?? $user->id;
        $stockRequest = StockRequest::where('store_id', $storeId)
            ->with(['product', 'store'])
            ->findOrFail($id);
        return view('inventory.show', compact('stockRequest'));
    }

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

    public function adjustments()
    {
        $user = Auth::user();
        $storeId = $user->store_id ?? $user->id;
        $adjustments = StockAdjustment::where('store_id', $storeId)->with(['product', 'user'])->latest()->paginate(15);
        $products = Product::where('is_active', true)->select('id', 'product_name', 'sku', 'unit')->get();
        return view('inventory.adjustments', compact('adjustments', 'products'));
    }

    public function storeAdjustment(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'operation' => 'required|in:add,subtract',
            'reason' => 'nullable|string|max:255',
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
                'store_id' => $storeId,
                'product_id' => $request->product_id,
                'user_id' => $user->id,
                'quantity' => $request->quantity,
                'operation' => $request->operation,
                'reason' => $request->reason,
            ]);
        });
        return back()->with('success', 'Stock adjusted successfully.');
    }

    public function history($id)
    {
        $user = Auth::user();
        $storeId = $user->store_id ?? $user->id;
        $product = Product::findOrFail($id);
        $transactions = StockTransaction::where('store_id', $storeId)
            ->where('product_id', $id)
            ->with(['store.user'])
            ->latest()
            ->paginate(20);
        return view('inventory.history', compact('product', 'transactions'));
    }
}