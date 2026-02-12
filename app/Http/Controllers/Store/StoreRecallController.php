<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\RecallRequest;
use App\Models\StoreStock;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\StockTransaction;
use App\Models\StoreDetail;
use App\Models\ProductBatch;
use App\Models\StoreNotification;
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

            // TAB: Recalls
            if ($tab === 'recalls') {
                $query = RecallRequest::where('store_id', $storeId)->with(['product', 'initiator'])->select('recall_requests.*');

                return DataTables::of($query)
                    ->editColumn('id', fn($row) => '#' . str_pad($row->id, 5, '0', STR_PAD_LEFT))
                    ->addColumn('product_name', fn($row) => $row->product->product_name ?? 'N/A')
                    ->editColumn('reason', fn($row) => ucwords(str_replace('_', ' ', $row->reason)))
                    ->editColumn('status', function ($row) {
                        $badge = match ($row->status) {
                            'pending_warehouse_approval', 'pending_store_approval' => 'warning',
                            'completed', 'dispatched' => 'success',
                            'rejected', 'rejected_by_store' => 'danger',
                            default => 'primary'
                        };
                        return '<span class="badge bg-' . $badge . '">' . ucwords(str_replace('_', ' ', $row->status)) . '</span>';
                    })
                    ->addColumn('initiator_name', fn($row) => $row->initiator->name ?? 'System')
                    ->editColumn('created_at', fn($row) => $row->created_at->format('d M Y H:i'))
                    ->addColumn('action', function ($row) {
                        return '<a href="' . route('store.stock-control.recall.show', $row->id) . '" class="btn btn-sm btn-outline-primary"><i class="mdi mdi-eye me-1"></i> View</a>';
                    })
                    ->rawColumns(['status', 'action'])
                    ->make(true);
            }

            if ($tab === 'expiry') {
                $query = ProductBatch::query()
                    ->join('products', 'product_batches.product_id', '=', 'products.id')
                    ->leftJoin('product_categories', 'products.category_id', '=', 'product_categories.id')
                    ->where('product_batches.store_id', $storeId)
                    ->where('product_batches.quantity', '>', 0)
                    ->select([
                        'product_batches.id as batch_id', // Alias ID to avoid ambiguity
                        'product_batches.*',
                        'products.product_name',
                        'products.sku',
                        'product_categories.name as category_name',
                        // POSTGRESQL FIX: Subtract dates directly
                        DB::raw("(product_batches.expiry_date - CURRENT_DATE) as days_left")
                    ]);

                return DataTables::of($query)
                    ->editColumn('expiry_date', fn($row) => $row->expiry_date ? Carbon::parse($row->expiry_date)->format('d M Y') : '-')
                    ->addColumn('status', function ($row) {
                        $daysLeft = $row->days_left;
                        if ($daysLeft !== null && $daysLeft < 0) return '<span class="badge bg-danger">Expired</span>';
                        if ($daysLeft !== null && $daysLeft <= 30) return '<span class="badge bg-warning text-dark">Expiring Soon</span>';
                        return '<span class="badge bg-secondary">OK</span>';
                    })
                    ->addColumn('action', function ($row) {
                        // Ensure you use the aliased 'batch_id' or 'id' correctly
                        return '<a href="' . route('store.stock-control.recall.create', ['batch_id' => $row->batch_id]) . '" class="btn btn-sm btn-outline-danger shadow-sm"><i class="mdi mdi-undo-variant me-1"></i> Recall</a>';
                    })
                    ->rawColumns(['status', 'action'])
                    ->make(true);
            }


            if ($tab === 'lowstock') {
                $query = StoreStock::where('store_id', $storeId)
                    ->where('quantity', '<', 10)
                    ->with(['product.category']);

                return DataTables::of($query)
                    ->addColumn('product_name', fn($row) => $row->product->product_name ?? 'N/A')
                    ->addColumn('sku', fn($row) => $row->product->sku ?? '-')
                    ->addColumn('category_name', fn($row) => $row->product->category->name ?? '-')
                    ->editColumn('quantity', fn($row) => '<span class="badge bg-danger fs-6">' . $row->quantity . '</span>')
                    ->addColumn('reorder_suggestion', fn($row) => 'Target: 20 (Order: ' . max(0, 20 - $row->quantity) . ')')
                    ->rawColumns(['quantity'])
                    ->make(true);
            }
        }

        $expiryCount = ProductBatch::where('store_id', $storeId)
            ->where('quantity', '>', 0)
            ->where(function ($q) {
                $q->where('expiry_date', '<=', now()->addDays(60));
            })
            ->count();

        $lowStockCount = StoreStock::where('store_id', $storeId)->where('quantity', '<', 10)->count();

        return view('store.stock-control.recall.index', compact('expiryCount', 'lowStockCount'));
    }

    /**
     * Show create form
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
     * Store new recall
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
            return back()->withErrors(['quantity' => 'Insufficient stock. You only have ' . ($stock->quantity ?? 0) . ' units.'])->withInput();
        }

        RecallRequest::create([
            'store_id' => $storeId,
            'product_id' => $request->product_id,
            'requested_quantity' => $request->quantity,
            'reason' => $request->reason,
            'reason_remarks' => $request->reason_remarks,
            'status' => 'pending_warehouse_approval',
            'initiated_by' => $user->id,
        ]);

        // [NOTIFICATION]
        StoreNotification::create([
            'user_id' => Auth::id(),
            'store_id' => Auth::user()->store_id,
            'title' => 'Recall Request Created',
            'message' => "Recall initiated for Batch: {$request->batch_number}.",
            'type' => 'info',
            'url' => route('store.stock-control.recall.index'),
        ]);

        return redirect()->route('store.stock-control.recall.index')->with('success', 'Recall request created. Waiting for Warehouse approval.');
    }

    /**
     * Show recall details
     */
    public function show(RecallRequest $recall)
    {
        $user = Auth::user();
        $storeId = $user->store_id;

        if ($recall->store_id != $storeId) abort(403);

        $recall->load(['product', 'initiator']);

        $batches = ProductBatch::where('product_id', $recall->product_id)
            ->where('store_id', $storeId)
            ->where('quantity', '>', 0)
            ->get();

        return view('store.stock-control.recall.show', compact('recall', 'batches'));
    }

    public function updateStatus(Request $request, $id)
    {
        $recall = RecallRequest::findOrFail($id);

        $request->validate([
            'status' => 'required|in:approved_by_store,rejected',
            'approved_quantity' => 'nullable|integer|min:0',
            'remarks' => 'nullable|string|max:500'
        ]);

        // Logic: Agar approve ho raha hai to input wali qty lo, warna 0
        $finalApprovedQty = ($request->status == 'approved_by_store')
            ? ($request->approved_quantity ?? $recall->requested_quantity)
            : 0;

        $recall->update([
            'status' => $request->status,
            'approved_quantity' => $finalApprovedQty,
            'store_remarks' => $request->remarks,
            'approved_by_store_user_id' => Auth::id(),
            // 'updated_at' automatic handle hota hai
        ]);

        $msg = $request->status == 'approved_by_store' ? 'Approved' : 'Rejected';
        return redirect()->back()->with('success', "Recall request has been $msg successfully.");
    }

    /**
     * Dispatch selected batches back to warehouse
     */
    public function dispatch(Request $request, RecallRequest $recall)
    {
        $user = Auth::user();
        $storeId = $user->store_id;

        if ($recall->store_id != $storeId) abort(403);

        $request->validate([
            'batches' => 'required|array',
            'batches.*.batch_id' => 'required|exists:product_batches,id',
            'batches.*.quantity' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request, $recall, $storeId) {
            $totalDispatched = 0;

            foreach ($request->batches as $batchData) {
                $batch = ProductBatch::where('id', $batchData['batch_id'])
                    ->where('store_id', $storeId)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($batch->quantity < $batchData['quantity']) {
                    throw new \Exception("Insufficient quantity in batch " . $batch->id);
                }

                $batch->decrement('quantity', $batchData['quantity']);
                $totalDispatched += $batchData['quantity'];

                $detail = StoreDetail::find($storeId);

                StockTransaction::create([
                    'product_id' => $recall->product_id,
                    'product_batch_id' => $batch->id,
                    'store_id' => $storeId,
                    'type' => 'recall_out',
                    'quantity_change' => -$batchData['quantity'],
                    'running_balance' => $batch->quantity,
                    'reference_id' => 'RECALL-' . $recall->id,
                    'remarks' => 'Dispatched to Warehouse (Recall)',
                    'warehouse_id' => $detail?->warehouse_id,
                ]);
            }

            $storeStock = StoreStock::where('store_id', $storeId)
                ->where('product_id', $recall->product_id)
                ->first();

            if ($storeStock) {
                $storeStock->decrement('quantity', $totalDispatched);
            }

            $recall->update([
                'dispatched_quantity' => $totalDispatched,
                'status' => 'dispatched',
            ]);
        });

        return back()->with('success', 'Stock dispatched to Warehouse successfully.');
    }

    public function downloadChallan(RecallRequest $recall)
    {
        $user = Auth::user();
        if ($recall->store_id != $user->store_id) abort(403);

        if (!in_array($recall->status, ['dispatched', 'received', 'completed'])) {
            return back()->with('error', 'Challan is only available after dispatch.');
        }

        $recall->load(['product', 'store', 'initiator']);
        $pdf = Pdf::loadView('pdf.delivery-challan', compact('recall'));
        return $pdf->download('Recall_Challan_#' . $recall->id . '.pdf');
    }
}