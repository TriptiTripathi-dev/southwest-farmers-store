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
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StockRequestImport;
use App\Models\RecallRequest;
use App\Models\StoreNotification;

class StoreInventoryController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = StoreStock::where('store_id', $user->store_id)
            ->with(['product.category', 'product.subcategory']);
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('product_name', 'ilike', "%{$search}%")
                    ->orWhere('sku', 'ilike', "%{$search}%")
                    ->orWhere('barcode', 'ilike', "%{$search}%");
            });
        }
        $stocks = $query->latest()->paginate(15);
        $inTransitByProduct = StockRequest::where('store_id', $user->store_id)
            ->where('status', StockRequest::STATUS_DISPATCHED)
            ->select('product_id', DB::raw('SUM(COALESCE(fulfilled_quantity, requested_quantity)) as qty'))
            ->groupBy('product_id')
            ->pluck('qty', 'product_id');

        return view('inventory.index', compact('stocks', 'inTransitByProduct'));
    }

    public function stockReport(Request $request)
    {
        $storeId = Auth::user()->store_id;

        // Base Query
        $query = StoreStock::where('store_stocks.store_id', $storeId)
            ->join('products', 'store_stocks.product_id', '=', 'products.id')
            ->leftJoin('product_categories', 'products.category_id', '=', 'product_categories.id')
            ->select(
                'store_stocks.*',
                'products.product_name',
                'products.sku',
                'products.price', // Selling Price
                'products.cost_price',
                'product_categories.name as category_name'
            );

        // 1. Filter by Category
        if ($request->has('category') && $request->category != 'all') {
            $query->where('products.category_id', $request->category);
        }

        // 2. Filter by Stock Status
        if ($request->has('status')) {
            if ($request->status == 'low') {
                // Low Stock Logic: Qty <= Min Level
                $query->where('store_stocks.quantity', '>', 0);
            } elseif ($request->status == 'out') {
                $query->where('store_stocks.quantity', '=', 0);
            } elseif ($request->status == 'in') {
                $query->where('store_stocks.quantity', '>', 0);
            }
        }

        // 3. Search (PostgreSQL ILIKE)
        if ($request->has('search') && $request->search != '') {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('products.product_name', 'ILIKE', "%{$term}%")
                    ->orWhere('products.sku', 'ILIKE', "%{$term}%");
            });
        }

        // Get Data (Pagination)
        $stocks = $query->orderBy('products.product_name', 'asc')->paginate(15);

        // Summary Calculations (Cards ke liye)
        $totalItems = StoreStock::where('store_id', $storeId)->count();
        $totalQty = StoreStock::where('store_id', $storeId)->sum('quantity');

        // Total Value Calculation (Qty * Price)
        // Note: Accurate value ke liye hume saare records lene honge, pagination nahi
        $allStocks = StoreStock::where('store_stocks.store_id', $storeId)
            ->join('products', 'store_stocks.product_id', '=', 'products.id')
            ->select('store_stocks.quantity', 'products.price')
            ->get();

        $totalValue = $allStocks->sum(function ($stock) {
            return $stock->quantity * $stock->price;
        });

        // Categories for Filter Dropdown
        $categories = \App\Models\ProductCategory::orderBy('name')->get();

        return view('store.reports.stock', compact('stocks', 'totalItems', 'totalQty', 'totalValue', 'categories'));
    }

    public function requestStock(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'store_remarks' => 'required|string|max:500',
        ]);

        $user = Auth::user();
        $storeId = $user->store_id;
        $productId = (int) $request->product_id;
        $requestedQty = (int) $request->quantity;

        $stock = StoreStock::where('store_id', $storeId)
            ->where('product_id', $productId)
            ->first();

        if (! $stock) {
            return back()->with('error', 'This product is not mapped to your store inventory yet.');
        }

        $hasOpenRequest = StockRequest::where('store_id', $storeId)
            ->where('product_id', $productId)
            ->whereIn('status', [StockRequest::STATUS_PENDING, StockRequest::STATUS_DISPATCHED])
            ->exists();

        if ($hasOpenRequest) {
            return back()->with('error', 'An open request already exists for this product (pending or in transit).');
        }

        $inTransitQty = $this->getInTransitQuantity($storeId, $productId);
        $availableQty = (int) $stock->quantity + $inTransitQty;
        $maxStock = (int) ($stock->max_stock ?? 0);

        if ($maxStock > 0) {
            $allowedQty = max(0, $maxStock - $availableQty);

            if ($allowedQty <= 0) {
                return back()->with('error', 'Order blocked. You already have enough stock available (current + in transit).');
            }

            if ($requestedQty > $allowedQty) {
                return back()->with('error', "Requested quantity exceeds allowed need. Max allowed right now is {$allowedQty}.");
            }
        }

        StockRequest::create([
            'store_id' => $storeId,
            'product_id' => $productId,
            'requested_quantity' => $requestedQty,
            'status' => StockRequest::STATUS_PENDING,
            'store_remarks' => trim((string) $request->store_remarks),
        ]);

        return back()->with('success', 'Stock requisition sent to Warehouse successfully!');
    }

    public function generateWarehousePo()
    {
        $storeId = Auth::user()->store_id;
        $stocks = StoreStock::where('store_id', $storeId)
            ->whereColumn('quantity', '<=', 'min_stock')
            ->where('max_stock', '>', 0)
            ->get(['product_id', 'quantity', 'max_stock']);

        if ($stocks->isEmpty()) {
            return back()->with('error', 'No items are currently below minimum stock.');
        }

        $created = 0;
        $skipped = 0;

        DB::transaction(function () use ($stocks, $storeId, &$created, &$skipped) {
            foreach ($stocks as $stock) {
                $hasOpenRequest = StockRequest::where('store_id', $storeId)
                    ->where('product_id', $stock->product_id)
                    ->whereIn('status', [StockRequest::STATUS_PENDING, StockRequest::STATUS_DISPATCHED])
                    ->exists();

                if ($hasOpenRequest) {
                    $skipped++;
                    continue;
                }

                $inTransitQty = $this->getInTransitQuantity($storeId, (int) $stock->product_id);
                $availableQty = (int) $stock->quantity + $inTransitQty;
                $targetMax = (int) $stock->max_stock;
                $requestedQty = max(0, $targetMax - $availableQty);

                if ($requestedQty <= 0) {
                    $skipped++;
                    continue;
                }

                StockRequest::create([
                    'store_id' => $storeId,
                    'product_id' => $stock->product_id,
                    'requested_quantity' => $requestedQty,
                    'status' => StockRequest::STATUS_PENDING,
                    'store_remarks' => 'Auto-generated by "Generate a PO to the Warehouse" (max-min replenish).',
                ]);

                $created++;
            }
        });

        if ($created === 0) {
            return back()->with('error', 'No new PO lines were generated. Existing open requests or sufficient in-transit stock prevented creation.');
        }

        $message = "Generated {$created} PO line(s) to warehouse.";
        if ($skipped > 0) {
            $message .= " Skipped {$skipped} item(s) due to open requests or sufficient available stock.";
        }

        return back()->with('success', $message);
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
            ->select('id', 'product_name', 'sku', 'barcode', 'unit')
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

    private function getInTransitQuantity(int $storeId, int $productId): int
    {
        return (int) StockRequest::where('store_id', $storeId)
            ->where('product_id', $productId)
            ->where('status', StockRequest::STATUS_DISPATCHED)
            ->sum(DB::raw('COALESCE(fulfilled_quantity, requested_quantity)'));
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

        // StoreInventoryController.php lines 136-141 ko replace karein
        if ($request->hasFile('store_payment_proof')) {
            try {

                $path = Storage::putFile(
                    'payment_proofs',
                    $request->file('store_payment_proof')
                );
                if (!$path) {
                    return response()->json(['success' => false, 'message' => 'Upload failed without error message.'], 500);
                }

                $stockRequest->update([
                    'store_payment_proof' => $path,
                    'store_remarks' => $request->store_remarks
                ]);

                return response()->json(['success' => true, 'message' => 'Payment proof uploaded successfully!', 'path' => $path]);
            } catch (\Exception $e) {
                \Log::error('S3 Upload Error: ' . $e->getMessage());
                return response()->json(['success' => false, 'message' => 'S3 Error: ' . $e->getMessage()], 500);
            }
        }
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

        try {
            DB::transaction(function () use ($request, $user, $storeId) {
                $stock = StoreStock::firstOrNew(['store_id' => $storeId, 'product_id' => $request->product_id]);

                if ($request->operation === 'add') {
                    $stock->quantity = ($stock->quantity ?? 0) + $request->quantity;
                } else {
                    if (($stock->quantity ?? 0) < $request->quantity) {
                        // Throw exception to be caught below
                        throw new \Exception("Insufficient stock. Only " . ($stock->quantity ?? 0) . " unit(s) available.");
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

                StoreNotification::create([
                    'user_id' => Auth::id(),
                    'store_id' => $storeId,
                    'title' => 'Stock Adjustment',
                    'message' => "Adjusted stock for Product #{$request->product_id} ({$request->operation} {$request->quantity}).",
                    'type' => 'warning',
                    'url' => route('inventory.adjustments'),
                ]);
            });

            return back()->with('success', 'Stock adjusted successfully.');
        } catch (\Exception $e) {
            // CATCH THE ERROR AND SEND IT BACK TO BLADE
            return back()->with('error', $e->getMessage());
        }
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

        // Append batch info to remarks if coming from specific batch
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

        // [NOTIFICATION]
        StoreNotification::create([
            'user_id' => Auth::id(),
            'store_id' => Auth::user()->store_id,
            'title' => 'Recall Request Created',
            'message' => "Recall initiated for Batch: {$request->batch_number}.",
            'type' => 'info',
            'url' => route('store.stock-control.recall.index'),
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
     * Dispatch logic.
     */
    public function dispatch(Request $request, RecallRequest $recall)
    {
        $user = Auth::user();
        $storeId = $user->store_id;

        if ($recall->store_id != $storeId) abort(403);

        if (!in_array($recall->status, ['approved', 'approved_by_store'])) {
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

            if ($storeStock) $storeStock->decrement('quantity', $totalDispatched);

            $recall->update([
                'dispatched_quantity' => $totalDispatched,
                'status' => 'dispatched',
            ]);
        });

        return back()->with('success', 'Stock dispatched successfully.');
    }
}
