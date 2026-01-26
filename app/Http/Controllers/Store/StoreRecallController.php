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
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class StoreRecallController extends Controller
{
    /**
     * Display the Recall Dashboard with 3 Tabs.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $storeId = $user->store_id;

        if ($request->ajax()) {
            $tab = $request->get('tab');

            // --- TAB 1: RECALL HISTORY ---
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
                            'pending_warehouse_approval', 'pending_store_approval', 'pending' => 'warning',
                            'approved', 'approved_by_store' => 'info',
                            'completed', 'dispatched', 'received' => 'success',
                            'rejected', 'rejected_by_store' => 'danger',
                            default => 'primary'
                        };
                        return '<span class="badge bg-'.$badge.'">'.ucwords(str_replace('_', ' ', $row->status)).'</span>';
                    })
                    ->editColumn('created_at', fn($row) => $row->created_at->format('d M Y H:i'))
                    ->addColumn('action', function ($row) {
                        return '<a href="'.route('store.stock-control.recall.show', $row->id).'" class="btn btn-sm btn-outline-primary"><i class="mdi mdi-eye me-1"></i> View</a>';
                    })
                    ->rawColumns(['status', 'action'])
                    ->make(true);
            }

            // --- TAB 2: EXPIRY & DAMAGE ALERTS ---
            if ($tab === 'expiry') {
                $query = ProductBatch::query()
                    ->join('products', 'product_batches.product_id', '=', 'products.id')
                    ->leftJoin('product_categories', 'products.category_id', '=', 'product_categories.id')
                    ->where('product_batches.store_id', $storeId)
                    ->where('product_batches.quantity', '>', 0)
                    ->where(function($q) {
                        $q->where('product_batches.expiry_date', '<=', now()->addDays(60))
                          ->orWhere('product_batches.damaged_quantity', '>', 0);
                    })
                    ->select([
                        'product_batches.id as batch_id',
                        'product_batches.*',
                        'products.product_name',
                        'products.sku',
                        'product_categories.name as category_name',
                        DB::raw("(product_batches.expiry_date - CURRENT_DATE) as days_left")
                    ]);

                return DataTables::of($query)
                    ->editColumn('expiry_date', fn($row) => $row->expiry_date ? Carbon::parse($row->expiry_date)->format('d M Y') : '-')
                    ->editColumn('damaged_quantity', fn($row) => $row->damaged_quantity > 0 ? '<span class="text-danger fw-bold">'.$row->damaged_quantity.'</span>' : '0')
                    ->addColumn('status', function ($row) {
                        if ($row->days_left <= 0) return '<span class="badge bg-danger">Expired</span>';
                        if ($row->days_left <= 30) return '<span class="badge bg-warning text-dark">Expiring</span>';
                        if ($row->damaged_quantity > 0) return '<span class="badge bg-secondary">Damaged</span>';
                        return '<span class="badge bg-info">Check</span>';
                    })
                    ->addColumn('action', function($row) {
                        return '<a href="'.route('store.stock-control.recall.create', ['batch_id' => $row->batch_id]).'" class="btn btn-sm btn-outline-danger shadow-sm"><i class="mdi mdi-undo-variant me-1"></i> Recall</a>';
                    })
                    ->rawColumns(['damaged_quantity', 'status', 'action'])
                    ->make(true);
            }

            // --- TAB 3: LOW STOCK ALERTS ---
            if ($tab === 'lowstock') {
                $query = StoreStock::where('store_id', $storeId)
                    ->where('quantity', '<', 10)
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

        $expiryCount = ProductBatch::where('store_id', $storeId)
            ->where('quantity', '>', 0)
            ->where(function($q) { $q->where('expiry_date', '<=', now()->addDays(60))->orWhere('damaged_quantity', '>', 0); })
            ->count();

        $lowStockCount = StoreStock::where('store_id', $storeId)->where('quantity', '<', 10)->count();

        return view('store.stock-control.recall.index', compact('expiryCount', 'lowStockCount'));
    }

    /**
     * Show form to create a request.
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        $storeId = $user->store_id;

        $products = StoreStock::where('store_id', $storeId)
            ->where('quantity', '>', 0)
            ->with('product')
            ->get();

        $selectedBatch = null;
        if ($request->batch_id) {
            $selectedBatch = ProductBatch::with('product')->where('store_id', $storeId)->find($request->batch_id);
        }

        return view('store.stock-control.recall.create', compact('products', 'selectedBatch'));
    }

    /**
     * Store the request.
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

        $stock = StoreStock::where('store_id', $storeId)
            ->where('product_id', $request->product_id)
            ->first();

        if (!$stock || $stock->quantity < $request->quantity) {
            return back()->withErrors(['quantity' => 'Insufficient stock. Available: ' . ($stock->quantity ?? 0)])->withInput();
        }

        $remarks = $request->reason_remarks;
        if ($request->batch_id) {
            $batch = ProductBatch::find($request->batch_id);
            if ($batch) {
                $remarks .= " [Source Batch: " . $batch->batch_number . "]";
            }
        }

        RecallRequest::create([
            'store_id' => $storeId,
            'product_id' => $request->product_id,
            'requested_quantity' => $request->quantity,
            'reason' => $request->reason,
            'reason_remarks' => $remarks,
            'status' => 'pending_warehouse_approval',
            'initiated_by' => $user->id,
        ]);

        return redirect()->route('store.stock-control.recall.index')
            ->with('success', 'Recall Request created. Waiting for Warehouse approval.');
    }

    /**
     * Show details.
     */
    public function show(RecallRequest $recall)
    {
        $user = Auth::user();
        $storeId = $user->store_id;

        if ($recall->store_id != $storeId) abort(403);

        $recall->load(['product', 'initiator']);

        // Fetch batches belonging to THIS Store for dispatch
        $batches = ProductBatch::where('product_id', $recall->product_id)
            ->where('store_id', $storeId)
            ->where('quantity', '>', 0)
            ->orderBy('expiry_date', 'asc')
            ->get();

        return view('store.stock-control.recall.show', compact('recall', 'batches'));
    }

    /**
     * Approve Recall (For Warehouse-Initiated Requests).
     */
    public function approve(Request $request, RecallRequest $recall)
    {
        $user = Auth::user();
        $storeId = $user->store_id;

        if ($recall->store_id != $storeId) abort(403);
        if ($recall->status !== 'pending_store_approval') {
            return back()->with('error', 'This request is not pending your approval.');
        }

        $request->validate([
            'approved_quantity' => 'required|integer|min:1|max:' . $recall->requested_quantity,
            'store_remarks' => 'nullable|string'
        ]);

        $recall->update([
            'approved_quantity' => $request->approved_quantity,
            'store_remarks' => $request->store_remarks,
            'status' => 'approved_by_store', // Ready for dispatch
            'approved_by_store_user_id' => $user->id
        ]);

        return back()->with('success', 'Recall Request Approved. You can now dispatch the items.');
    }

    /**
     * Reject Recall (For Warehouse-Initiated Requests).
     */
    public function reject(Request $request, RecallRequest $recall)
    {
        $user = Auth::user();
        $storeId = $user->store_id;

        if ($recall->store_id != $storeId) abort(403);
        if ($recall->status !== 'pending_store_approval') {
            return back()->with('error', 'This request is not pending your approval.');
        }

        $request->validate(['store_remarks' => 'required|string']);

        $recall->update([
            'store_remarks' => $request->store_remarks,
            'status' => 'rejected_by_store',
            'approved_by_store_user_id' => $user->id
        ]);

        return back()->with('success', 'Recall Request Rejected.');
    }

    /**
     * Dispatch logic.
     */
    public function dispatch(Request $request, RecallRequest $recall)
    {
        $user = Auth::user();
        $storeId = $user->store_id;

        if ($recall->store_id != $storeId) abort(403);

        // Allow dispatch if approved by Warehouse OR Approved by Store (for warehouse-initiated)
        if (!in_array($recall->status, ['approved', 'approved_by_store', 'partial_approved'])) {
            return back()->with('error', 'Request must be approved before dispatch.');
        }

        $request->validate([
            'batches' => 'required|array',
            'batches.*.batch_id' => 'required|exists:product_batches,id',
            'batches.*.quantity' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request, $recall, $user, $storeId) {
            $totalDispatched = 0;

            foreach ($request->batches as $batchData) {
                $batch = ProductBatch::where('id', $batchData['batch_id'])
                            ->where('store_id', $storeId)
                            ->lockForUpdate()
                            ->firstOrFail();

                if ($batch->quantity < $batchData['quantity']) {
                    throw new \Exception("Insufficient quantity in batch " . $batch->batch_number);
                }

                $batch->decrement('quantity', $batchData['quantity']);
                $totalDispatched += $batchData['quantity'];

                StockTransaction::create([
                    'product_id' => $recall->product_id,
                    'product_batch_id' => $batch->id,
                    'store_id' => $storeId,
                    'type' => 'recall_out',
                    'quantity_change' => -$batchData['quantity'],
                    'running_balance' => $batch->quantity,
                    'reference_id' => 'RECALL-' . $recall->id,
                    'remarks' => 'Dispatched to Warehouse',
                    'ware_user_id' => $user->id,
                ]);
            }

            $storeStock = StoreStock::where('store_id', $storeId)
                ->where('product_id', $recall->product_id)
                ->first();
            
            if($storeStock) $storeStock->decrement('quantity', $totalDispatched);

            $recall->update([
                'dispatched_quantity' => $totalDispatched,
                'status' => 'dispatched',
            ]);
        });

        return back()->with('success', 'Stock dispatched successfully.');
    }

    /**
     * Download PDF Challan.
     */
    public function downloadChallan(RecallRequest $recall)
    {
        $user = Auth::user();
        if ($recall->store_id != $user->store_id) abort(403);

        if (!in_array($recall->status, ['dispatched', 'received', 'completed'])) {
            return back()->with('error', 'Challan is only available after dispatch.');
        }

        $recall->load(['product', 'store', 'initiator']);
        $pdf = Pdf::loadView('pdf.recall-challan', compact('recall'));
        return $pdf->download('Recall_Challan_#' . $recall->id . '.pdf');
    }
}