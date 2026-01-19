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

        // Create the request
        StockRequest::create([
            'store_id'           => $user->store_id,
            'product_id'         => $request->product_id,
            'requested_quantity' => $request->quantity,
            'status'             => 'pending',
        ]);

        return back()->with('success', 'Stock requisition sent to Store successfully!');
    }

    // --- New Methods for Bulk Import ---

    public function downloadSampleCsv()
    {
        // Generates a simple CSV file on the fly
        return response()->streamDownload(function () {
            echo "sku,quantity\n";
            echo "STR-001,10\n";
            echo "STR-002,5\n";
            echo "STR-003,20\n";
        }, 'stock_request_sample.csv');
    }

    public function importStockRequests(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt,xlsx'
        ]);

        Excel::import(new StockRequestImport, $request->file('file'));

        return back()->with('success', 'Stock requests imported successfully.');
    }

    // Section 2: Stock Requests (CRUD with Search & Select)
    public function requests(Request $request)
    {
        $user = Auth::user();

        // 1. Prepare Request Query
        // Note: I assumed you meant $user->store_id in the first check based on previous context
        $query = StockRequest::where('store_id', $user->store_id ?? $user->id)
            ->with('product');

        // 2. Apply Search Filter (Product Name or SKU)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('product', function($q) use ($search) {
                // Changed 'like' to 'ilike' for case-insensitive search (PostgreSQL)
                $q->where('product_name', 'ilike', "%{$search}%")
                  ->orWhere('sku', 'ilike', "%{$search}%");
            });
        }

        // 3. Apply Status Select Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->latest()->paginate(15);

        // 4. Fetch Products for "New Request" Dropdown
        $products = \App\Models\Product::where('is_active', true)
            ->select('id', 'product_name', 'sku', 'unit')
            ->orderBy('product_name')
            ->get();

        return view('inventory.requests', compact('requests', 'products'));
    }

    // Cancel a Pending Request
    public function cancelRequest($id)
    {
        $user = Auth::user();
        
        $stockRequest = StockRequest::where('id', $id)
            ->where('store_id', $user->id ?? $user->id)
            ->firstOrFail();

        if ($stockRequest->status == 'pending') {
            $stockRequest->delete();
            return back()->with('success', 'Stock request cancelled successfully.');
        }

        return back()->with('error', 'Cannot cancel a request that has already been processed.');
    }

    public function adjustments()
    {
        $user = Auth::user();
        $storeId = $user->store_id ?? $user->id;

        // 1. Fetch Adjustment History
        $adjustments = StockAdjustment::where('store_id', $storeId)
            ->with(['product', 'user'])
            ->latest()
            ->paginate(15);

        // 2. Fetch Active Products for the Dropdown
        $products = Product::where('is_active', true)
            ->select('id', 'product_name', 'sku', 'unit')
            ->orderBy('product_name')
            ->get();

        return view('inventory.adjustments', compact('adjustments', 'products'));
    }

    // Process the Adjustment
    public function storeAdjustment(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
            'operation'  => 'required|in:add,subtract',
            'reason'     => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        $storeId = $user->store_id ?? $user->id;

        // Use Transaction to ensure DB integrity
        DB::transaction(function () use ($request, $user, $storeId) {
            
            // 1. Find or Create the Stock Record
            $stock = StoreStock::firstOrNew([
                'store_id'   => $storeId,
                'product_id' => $request->product_id
            ]);

            if ($request->operation === 'add') {
                $stock->quantity = ($stock->quantity ?? 0) + $request->quantity;
            } else {
                // Prevent negative stock
                if (($stock->quantity ?? 0) < $request->quantity) {
                    throw new \Exception("Insufficient stock. Current: " . ($stock->quantity ?? 0));
                }
                $stock->quantity -= $request->quantity;
            }
            $stock->save();

            // 3. Log the Adjustment
            StockAdjustment::create([
                'store_id'   => $storeId,
                'product_id' => $request->product_id,
                'user_id'    => $user->id,
                'quantity'   => $request->quantity,
                'operation'  => $request->operation,
                'reason'     => $request->reason,
            ]);
        });

        return back()->with('success', 'Stock adjusted successfully.');
    }
}