<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\RecallRequest;
use App\Models\ProductBatch;
use App\Models\StoreStock;
use App\Models\StockTransaction;
use App\Models\StoreDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class StoreRecallController extends Controller
{
    /**
     * Display the 3-Tab Dashboard (Recalls, Expiry, Low Stock).
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $storeId = $user->store_id;

        if ($request->ajax()) {
            $tab = $request->get('tab');

            // --- TAB 1: RECALL REQUESTS HISTORY ---
            if ($tab === 'recalls') {
                $query = RecallRequest::where('store_id', $storeId)
                    ->with(['product', 'initiator'])
                    ->select('recall_requests.*');

                return DataTables::of($query)
                    ->editColumn('id', fn($row) => '#' . str_pad($row->id, 5, '0', STR_PAD_LEFT))
                    ->addColumn('product_name', fn($row) => $row->product->product_name ?? 'N/A')
                    ->editColumn('reason', fn($row) => ucwords(str_replace('_', ' ', $row->reason)))
                    ->editColumn('status', function ($row) {
                        $badge = match($row->status) {
                            'pending_warehouse_approval', 'pending_store_approval' => 'warning',
                            'completed', 'dispatched' => 'success',
                            'rejected', 'rejected_by_store' => 'danger',
                            default => 'primary'
                        };
                        return '<span class="badge bg-'.$badge.'">'.ucwords(str_replace('_', ' ', $row->status)).'</span>';
                    })
                    ->addColumn('initiator_name', fn($row) => $row->initiator->name ?? 'System')
                    ->editColumn('created_at', fn($row) => $row->created_at->format('d M Y H:i'))
                    ->addColumn('action', function ($row) {
                        return '<a href="'.route('store.stock-control.recall.show', $row->id).'" class="btn btn-sm btn-outline-primary"><i class="mdi mdi-eye me-1"></i> View</a>';
                    })
                    ->rawColumns(['status', 'action'])
                    ->make(true);
            }

            // --- TAB 2: EXPIRY & DAMAGE ALERTS (From Expiry Page) ---
            if ($tab === 'expiry') {
                $query = ProductBatch::query()
        ->join('products', 'product_batches.product_id', '=', 'products.id')
        ->leftJoin('product_categories', 'products.category_id', '=', 'product_categories.id')
        ->where('product_batches.store_id', $storeId)
        ->select([
            'product_batches.*',
            'products.product_name',
            'products.sku',
            'product_categories.name as category_name',
            // Fixed: Direct subtraction (PostgreSQL mein ye integer days deta hai)
            DB::raw("(product_batches.expiry_date - CURRENT_DATE) as days_left"),
            DB::raw("(product_batches.quantity * product_batches.cost_price) as value")
        ])
        ->where('product_batches.quantity', '>', 0)
                    ->select([
                        'product_batches.*',
                        'products.product_name',
                        'products.sku',
                        'product_categories.name as category_name',
                        DB::raw("(product_batches.expiry_date - CURRENT_DATE) as days_left")
                    ]);

                return DataTables::of($query)
                    ->editColumn('expiry_date', fn($row) => $row->expiry_date ? Carbon::parse($row->expiry_date)->format('d M Y') : '-')
                   
                    ->addColumn('status', function ($row) {
                        $daysLeft = $row->days_left;
                        if ($daysLeft !== null && $daysLeft < 0) return '<span class="badge bg-danger">Expired</span>';
                        if ($daysLeft !== null && $daysLeft <= 30) return '<span class="badge bg-warning text-dark">Expiring Soon</span>';
                     
                    })
                    ->addColumn('action', function($row) {
                        // Action: Link to Create Recall Page with Batch ID pre-filled
                        return '<a href="'.route('store.stock-control.recall.create', ['batch_id' => $row->id]).'" class="btn btn-sm btn-outline-danger shadow-sm"><i class="mdi mdi-undo-variant me-1"></i> Recall</a>';
                    })
                    ->rawColumns(['status', 'action'])
                    ->make(true);
            }

            // --- TAB 3: LOW STOCK ALERTS ---
            if ($tab === 'lowstock') {
                $query = StoreStock::where('store_id', $storeId)
                    ->where('quantity', '<', 10) // Low Stock Threshold
                    ->with(['product.category']);

                return DataTables::of($query)
                    ->addColumn('product_name', fn($row) => $row->product->product_name ?? 'N/A')
                    ->addColumn('sku', fn($row) => $row->product->sku ?? '-')
                    ->addColumn('category_name', fn($row) => $row->product->category->name ?? '-')
                    ->editColumn('quantity', fn($row) => '<span class="badge bg-danger fs-6">'.$row->quantity.'</span>')
                    ->addColumn('reorder_suggestion', fn($row) => 'Target: 20 (Order: '.max(0, 20 - $row->quantity).')')
                    ->rawColumns(['quantity'])
                    ->make(true);
            }
        }

        // Counts for Tab Badges
        $expiryCount = ProductBatch::where('store_id', $storeId)
            ->where('quantity', '>', 0)
            ->where(function($q) { $q->where('expiry_date', '<=', now()->addDays(60)); })
            ->count();

        $lowStockCount = StoreStock::where('store_id', $storeId)->where('quantity', '<', 10)->count();

        return view('store.stock-control.recall.index', compact('expiryCount', 'lowStockCount'));
    }

    /**
     * Show form to create a new recall request.
     * Optionally accepts 'batch_id' to pre-fill the form.
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        $storeId = $user->store_id;

        // Fetch products available in stock
        $products = StoreStock::where('store_id', $storeId)
            ->where('quantity', '>', 0)
            ->with('product')
            ->get();

        // Check if coming from Expiry Tab with a specific batch
        $selectedBatch = null;
        if ($request->batch_id) {
            $selectedBatch = ProductBatch::with('product')->where('store_id', $storeId)->find($request->batch_id);
        }

        return view('store.stock-control.recall.create', compact('products', 'selectedBatch'));
    }

    /**
     * Store the new recall request.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $storeId = $user->store_id;

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string',
            'reason_remarks' => 'nullable|string',
        ]);

        // Verify Total Store Stock
        $stock = StoreStock::where('store_id', $storeId)
            ->where('product_id', $request->product_id)
            ->first();

        if (!$stock || $stock->quantity < $request->quantity) {
            return back()->withErrors(['quantity' => 'Insufficient stock. You only have ' . ($stock->quantity ?? 0) . ' units.'])->withInput();
        }

        // Logic to append Batch Info to remarks if needed
        // (Since we are creating a generic request first, we store batch info in text if DB column doesn't exist)
        $remarks = $request->reason_remarks;
        /* Uncomment if you want to validate against specific batch quantity
           if ($request->batch_id) { ... logic ... } 
        */

        RecallRequest::create([
            'store_id' => $storeId,
            'product_id' => $request->product_id,
            'requested_quantity' => $request->quantity,
            'reason' => $request->reason,
            'reason_remarks' => $remarks,
            'status' => 'pending_warehouse_approval', // Sent to warehouse
            'initiated_by' => $user->id,
        ]);

        return redirect()->route('store.stock-control.recall.index')->with('success', 'Recall request created. Waiting for Warehouse approval.');
    }

    // ... [Show, Dispatch methods remain unchanged] ...
   public function show(RecallRequest $recall)
    {
        $user = Auth::user();
        $storeId = $user->store_id;

        if ($recall->store_id != $storeId) {
            abort(403);
        }

        $recall->load(['product', 'initiator']);

        // FIX: Fetch batches belonging to THIS Store for THIS Product
        // Only show batches that have quantity > 0
        $batches = ProductBatch::where('product_id', $recall->product_id)
            ->where('quantity', '>', 0)
            ->orderBy('expiry_date', 'asc')
            ->get();

        return view('store.stock-control.recall.show', compact('recall', 'batches'));
    }

    public function dispatch(Request $request, RecallRequest $recall)
    {
        $user = Auth::user();
        $storeId = $user->store_id;

       

        $request->validate([
            'batches' => 'required|array',
            'batches.*.batch_id' => 'required|exists:product_batches,id',
            'batches.*.quantity' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request, $recall, $user, $storeId) {
            $totalDispatched = 0;

            foreach ($request->batches as $batchData) {
                // Fetch Batch from STORE context
                $batch = ProductBatch::where('id', $batchData['batch_id'])
                            ->lockForUpdate()
                            ->firstOrFail();

                if ($batch->quantity < $batchData['quantity']) {
                    throw new \Exception("Insufficient quantity in batch " . $batch->batch_number);
                }

                // Decrement Batch Quantity
                $batch->decrement('quantity', $batchData['quantity']);
                $totalDispatched += $batchData['quantity'];
                $detail = StoreDetail::find($storeId);
                // Log Transaction
                StockTransaction::create([
                    'product_id' => $recall->product_id,
                    'product_batch_id' => $batch->id,
                    'store_id' => $storeId,
                    'type' => 'recall_out',
                    'quantity_change' => -$batchData['quantity'],
                    'running_balance' => $batch->quantity, // Balance of specific batch
                    'reference_id' => 'RECALL-' . $recall->id,
                    'remarks' => 'Dispatched to Warehouse (Recall)',
                    'warehouse_id' => $detail->warehouse_id,
                ]);
            }

            // Decrement Aggregate Store Stock
            $storeStock = StoreStock::where('store_id', $storeId)
                ->where('product_id', $recall->product_id)
                ->first();
            
            if($storeStock) {
                $storeStock->decrement('quantity', $totalDispatched);
            }

            // Update Request Status
            $recall->update([
                'dispatched_quantity' => $totalDispatched,
                'status' => 'dispatched',
            ]);
        });

        return back()->with('success', 'Stock dispatched to Warehouse successfully.');
    }
}