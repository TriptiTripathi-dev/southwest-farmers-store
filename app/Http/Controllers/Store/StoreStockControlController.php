<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StoreStock;
use App\Models\ProductCategory;
use Carbon\Carbon;
use App\Models\StockRequest;
use App\Models\ProductBatch;
use App\Models\StockTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;

class StoreStockControlController extends Controller
{
    public function overview()
    {
        $user = Auth::user();
        $storeId = $user->store_id ?? $user->id;

        $categories = ProductCategory::where('is_active', true)->get();

        // --- 1. Area-wise Total Sales (For Chart) ---
        $areaSales = StockTransaction::join('store_customers', 'stock_transactions.customer_id', '=', 'store_customers.id')
            ->where('stock_transactions.store_id', $storeId)
            ->where('stock_transactions.type', 'sale')
            ->whereNotNull('store_customers.address')
            ->select('store_customers.address', DB::raw('SUM(ABS(stock_transactions.quantity_change)) as total_sold'))
            ->groupBy('store_customers.address')
            ->orderByDesc('total_sold')
            ->get();

        $areaLabels = $areaSales->pluck('address');
        $areaData = $areaSales->pluck('total_sold');

        // --- 2. Top Selling Product Per Area (For Insights Table) ---
        // Finds the most popular item in each specific area
        $topProductsByArea = DB::table('stock_transactions as t')
            ->join('store_customers as c', 't.customer_id', '=', 'c.id')
            ->join('products as p', 't.product_id', '=', 'p.id')
            ->where('t.store_id', $storeId)
            ->where('t.type', 'sale')
            ->whereNotNull('c.address')
            ->select('c.address', 'p.product_name', DB::raw('SUM(ABS(t.quantity_change)) as qty'))
            ->groupBy('c.address', 'p.product_name')
            ->orderBy('c.address')
            ->orderByDesc('qty')
            ->get()
            ->groupBy('address')
            ->map(function ($group) {
                return $group->first(); // Returns the single top product row for the address
            });

        return view('store.stock-control.overview', compact('categories', 'areaLabels', 'areaData', 'topProductsByArea'));
    }

    public function overviewData(Request $request)
    {
        $user = Auth::user();
        $storeId = $user->store_id ?? $user->id;

        $query = StoreStock::where('store_id', $storeId)
            ->with(['product.category']);

        if ($request->category_id) {
            $query->whereHas('product', fn($q) => $q->where('category_id', $request->category_id));
        }

        if ($request->low_stock) {
            $query->where('quantity', '<', 10);
        }

        return DataTables::of($query)
            ->addColumn('product_name', fn($row) => $row->product->product_name ?? 'N/A')
            ->addColumn('upc', fn($row) => $row->product->upc ?? '-')
            ->addColumn('category_name', fn($row) => $row->product->category->name ?? '-')
            ->addColumn('quantity', fn($row) => $row->quantity)
            ->addColumn('selling_price', fn($row) => number_format($row->selling_price ?? 0, 2))
            ->addColumn('value', fn($row) => number_format($row->quantity * ($row->selling_price ?? 0), 2))
            ->make(true);
    }

    public function lowStock()
    {
        $categories = ProductCategory::where('is_active', true)->get();
        return view('store.stock-control.low-stock', compact('categories'));
    }

    public function lowStockData(Request $request)
    {
        $user = Auth::user();
        $storeId = $user->store_id;

        $query = StoreStock::where('store_id', $storeId)
            ->with(['product.category'])
            ->where('quantity', '<', 10);

        if ($request->category_id) {
            $query->whereHas('product', fn($q) => $q->where('category_id', $request->category_id));
        }

        return DataTables::of($query)
            ->addColumn('product_name', fn($row) => $row->product->product_name ?? 'N/A')
            ->addColumn('upc', fn($row) => $row->product->upc ?? '-')
            ->addColumn('category_name', fn($row) => $row->product->category->name ?? '-')
            ->addColumn('current_qty', fn($row) => $row->quantity)
            ->addColumn('min_level', fn($row) => 10)
            ->addColumn('suggested_reorder', fn($row) => max(0, 10 - $row->quantity))
            ->addColumn('action', fn($row) => '
                <button class="btn btn-sm btn-primary quick-request" 
                        data-product-id="' . $row->product_id . '" 
                        data-qty="' . max(0, 10 - $row->quantity) . '">
                    Request Now
                </button>
            ')
            ->make(true);
    }

    public function quickRequest(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $user = Auth::user();
        $storeId = $user->store_id;
        $product = Product::findOrFail($request->product_id);

        DB::transaction(function () use ($request, $user, $storeId, $product) {
            $po = StockRequest::create([
                'store_id' => $storeId,
                'request_number' => StockRequest::generateRequestNumber($storeId),
                'status' => 'pending',
                'requested_by' => $user->id,
                'store_remarks' => 'Quick request from low stock alert',
            ]);

            $po->items()->create([
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'unit_cost' => $product->cost_price ?? 0,
            ]);

            $po->calculateTotals();
        });

        return response()->json(['success' => true, 'message' => 'Stock request (PO) sent successfully']);
    }

    public function valuation(Request $request)
    {
        $user = Auth::user();
        $storeId = $user->store_id;

        // --- 1. Filters Setup ---
        $categoryId = $request->get('category_id');
        $productId = $request->get('product_id');

        // Default: Last 30 days
        $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date')) : Carbon::today()->subDays(29);
        $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date')) : Carbon::today();

        // Dropdown Data
        $categories = ProductCategory::where('is_active', true)->get();
        // Only show products that have ever been in this store to keep list manageable
        $productsList = Product::whereHas('storeStocks', function ($q) use ($storeId) {
            $q->where('store_id', $storeId);
        })->select('id', 'product_name')->get();


        // --- 2. Calculate Current Valuation (Filtered) ---
        // Base Query
        $stockQuery = StoreStock::where('store_stocks.store_id', $storeId)
            ->join('products', 'store_stocks.product_id', '=', 'products.id');

        if ($categoryId) {
            $stockQuery->where('products.category_id', $categoryId);
        }
        if ($productId) {
            $stockQuery->where('store_stocks.product_id', $productId);
        }

        // Current Total Value
        $storeValue = $stockQuery->sum(DB::raw('store_stocks.quantity * products.cost_price'));

        // --- 3. Top Products (Filtered) ---
        $topProducts = (clone $stockQuery)
            ->select([
                'products.id',
                'products.product_name',
                'products.upc',
                'store_stocks.quantity',
                DB::raw('store_stocks.quantity * products.cost_price as value')
            ])
            ->orderByDesc('value')
            ->limit(10)
            ->get();


        // --- 4. Trend Analysis (Advanced Rewind Logic) ---
        // We calculate daily changes to "rewind" the current value back to the start date.

        // Query daily value changes (Qty Change * Cost Price)
        $trendQuery = DB::table('stock_transactions')
            ->join('products', 'stock_transactions.product_id', '=', 'products.id')
            ->select(DB::raw('DATE(stock_transactions.created_at) as date'))
            ->selectRaw('SUM(stock_transactions.quantity_change * products.cost_price) as daily_value_change')
            ->where('stock_transactions.store_id', $storeId)
            ->whereBetween('stock_transactions.created_at', [
                $startDate->copy()->startOfDay(),
                Carbon::now()->endOfDay() // Fetch up to now to rewind correctly
            ])
            ->groupBy('date');

        if ($categoryId) {
            $trendQuery->where('products.category_id', $categoryId);
        }
        if ($productId) {
            $trendQuery->where('stock_transactions.product_id', $productId);
        }

        $dailyChanges = $trendQuery->get()->keyBy('date');

        // Rewind Loop
        $dates = [];
        $trendData = [];
        $currentTracker = $storeValue; // Start with current value

        // Loop from Today backwards to Start Date
        // We go slightly past end date if needed to calculate the "End Date Value" correctly
        $loopDate = Carbon::today();

        while ($loopDate->gte($startDate)) {
            $dateStr = $loopDate->format('Y-m-d');

            // If the loop date is within user's requested range, record it
            if ($loopDate->lte($endDate)) {
                $dates[] = $dateStr;
                $trendData[] = max(0, round($currentTracker, 2));
            }

            // Apply "Rewind": Subtract today's change to get yesterday's closing value
            // (Current - Change = Previous)
            $change = $dailyChanges[$dateStr]->daily_value_change ?? 0;
            $currentTracker -= $change;

            $loopDate->subDay();
        }

        // Arrays are currently in reverse chronological order (Today -> Past), flip them
        $dates = array_reverse($dates);
        $trendData = array_reverse($trendData);

        return view('store.stock-control.valuation', compact(
            'storeValue',
            'topProducts',
            'dates',
            'trendData',
            'categories',
            'productsList'
        ));
    }

    public function expiry()
    {
        $categories = ProductCategory::where('is_active', true)->get();
        return view('store.stock-control.expiry', compact('categories'));
    }

    public function expiryData(Request $request)
    {
        $user = Auth::user();
        $storeId = $user->store_id;

        $days = $request->days ?? 60;
        $categoryId = $request->category_id;
        $damagedOnly = $request->damaged_only == 1;

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
            ->where('product_batches.quantity', '>', 0);

        if ($days !== 'all') {
            $query->whereRaw("product_batches.expiry_date <= CURRENT_DATE + INTERVAL '{$days} days'");
        }

        if ($categoryId) {
            $query->where('products.category_id', $categoryId);
        }

        if ($damagedOnly) {
            $query->where('product_batches.damaged_quantity', '>', 0);
        }

        return DataTables::of($query)
            ->addColumn('status', function ($row) {
                $daysLeft = $row->days_left;
                if ($daysLeft <= 0) return '<span class="badge bg-danger">Expired</span>';
                if ($daysLeft <= 30) return '<span class="badge bg-danger">Critical (< 1mo)</span>';
                if ($daysLeft <= 90) return '<span class="badge bg-warning">Urgent (< 3mo)</span>';
                if ($daysLeft <= 180) return '<span class="badge bg-info">Warning (< 6mo)</span>';
                return '<span class="badge bg-success">Healthy</span>';
            })
            ->addColumn('action', fn($row) => '<button class="btn btn-sm btn-warning">Request Recall</button>')
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    public function requests(Request $request)
    {
        $user = Auth::user();
        $storeId = $user->store_id;

        $query = StockRequest::where('store_id', $storeId)
            ->with(['items.product', 'requestedBy']);

        // Filter by search (request number)
        if ($request->filled('search')) {
            $query->where('request_number', 'ILIKE', '%' . $request->search . '%');
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->latest()->paginate(15);

        return view('store.stock-control.requests', compact('requests'));
    }


    public function create()
    {
        return view('store.stock-control.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.unit_cost' => 'required|numeric|min:0',
        ]);

        $user = Auth::user();
        $storeId = $user->store_id;

        DB::transaction(function () use ($request, $user, $storeId) {
            // Create PO
            $po = StockRequest::create([
                'store_id' => $storeId,
                'request_number' => StockRequest::generateRequestNumber($storeId),
                'status' => 'pending',
                'requested_by' => $user->id,
                'store_remarks' => $request->remarks,
            ]);

            // Add line items
            foreach ($request->products as $item) {
                $po->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                ]);
            }

            // Calculate totals
            $po->calculateTotals();
        });

        return redirect()->route('store.stock-control.requests')
            ->with('success', 'Purchase Order created successfully');
    }

    public function show($id)
    {
        $user = Auth::user();
        $request = StockRequest::where('store_id', $user->store_id)
            ->with(['items.product', 'requestedBy', 'approvedBy'])
            ->findOrFail($id);

        return view('store.stock-control.show', compact('request'));
    }

    public function generateReplenishment()
    {
        $user = Auth::user();
        $storeId = $user->store_id;

        // Find items where (Stock + In Transit) < Min Stock
        $lowStocks = StoreStock::where('store_id', $storeId)
            ->whereRaw('(quantity + in_transit_qty) < min_stock')
            ->where('min_stock', '>', 0)
            ->with('product')
            ->get();

        if ($lowStocks->isEmpty()) {
            return back()->with('info', 'All items are currently above minimum stock levels.');
        }

        DB::transaction(function () use ($lowStocks, $user, $storeId) {
            $po = StockRequest::create([
                'store_id' => $storeId,
                'request_number' => StockRequest::generateRequestNumber($storeId),
                'status' => 'pending',
                'requested_by' => $user->id,
                'store_remarks' => 'Auto-generated replenishment for low stock items (Min/Max).',
            ]);

            foreach ($lowStocks as $stock) {
                $requirement = $stock->max_stock - ($stock->quantity + $stock->in_transit_qty);
                if ($requirement > 0) {
                    $po->items()->create([
                        'product_id' => $stock->product_id,
                        'quantity' => $requirement,
                        'unit_cost' => $stock->product->cost_price ?? 0,
                    ]);
                }
            }

            $po->calculateTotals();
        });

        return redirect()->route('store.stock-control.requests')
            ->with('success', 'Replenishment Purchase Order generated successfully for ' . $lowStocks->count() . ' items.');
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $request = StockRequest::where('store_id', $user->store_id)
            ->where('status', 'pending')
            ->findOrFail($id);

        $request->delete();

        return response()->json(['success' => true, 'message' => 'PO cancelled successfully']);
    }

    public function searchProducts(Request $request)
    {
        $user = Auth::user();
        $storeId = $user->store_id;
        $term = $request->term;
        $productId = $request->product_id;

        $query = Product::where('is_active', true);

        // Search by specific product ID
        if ($productId) {
            $query->where('id', $productId);
        }
        // Search by term
        elseif ($term) {
            $query->where(function ($q) use ($term) {
                $q->where('upc', 'ILIKE', "%{$term}%")
                    ->orWhere('product_name', 'ILIKE', "%{$term}%")
                    ->orWhere('upc', 'ILIKE', "%{$term}%");
            });
        }

        $products = $query->with(['storeStock' => function ($q) use ($storeId) {
            $q->where('store_id', $storeId);
        }])
            ->limit(20)
            ->get()
            ->map(function ($p) {
                $stock = $p->storeStock->first();
                return [
                    'id' => $p->id,
                    'upc' => $p->upc,
                    'product_name' => $p->product_name,
                    'unit_type' => $p->unit_type ?? 'units',
                    'current_stock' => $stock->quantity ?? 0,
                    'in_transit' => $stock->in_transit_qty ?? 0,
                    'cost_price' => $p->cost_price ?? 0,
                    'max_stock' => $stock->max_stock ?? 0,
                ];
            });

        return response()->json($products);
    }

    // Old method for backward compatibility
    public function storeRequest(Request $request)
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

        return redirect()->route('store.stock-control.requests')->with('success', 'Request sent');
    }

    public function received()
    {
        $user = Auth::user();
        $storeId = $user->store_id;

        // Fetch dispatched POs with their items
        $pending = StockRequest::where('store_id', $storeId)
            ->where('status', 'dispatched')
            ->with(['items.product'])
            ->latest()
            ->paginate(15);

        return view('store.stock-control.received', compact('pending'));
    }

    public function receive($id)
    {
        $user = Auth::user();
        $request = StockRequest::where('store_id', $user->store_id)
            ->where('status', 'dispatched')
            ->with(['items.product'])
            ->findOrFail($id);

        return view('store.stock-control.receive', compact('request'));
    }

    public function confirmReceived(Request $request, $id)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:stock_request_items,id',
            'items.*.received_quantity' => 'required|integer|min:0',
        ]);

        $user = Auth::user();
        $storeId = $user->store_id;

        $stockRequest = StockRequest::where('store_id', $storeId)
            ->where('status', 'dispatched')
            ->findOrFail($id);

        DB::transaction(function () use ($request, $stockRequest, $storeId) {
            foreach ($request->items as $itemData) {
                $item = $stockRequest->items()->findOrFail($itemData['id']);
                $receivedQty = $itemData['received_quantity'];

                if ($receivedQty > 0) {
                    $item->update([
                        'received_quantity' => $receivedQty
                    ]);

                    // Update store stock
                    $stock = StoreStock::firstOrCreate([
                        'store_id' => $storeId,
                        'product_id' => $item->product_id
                    ]);

                    $stock->increment('quantity', $receivedQty);

                    // Requirement 13.2: Create ProductBatch for traceability
                    ProductBatch::create([
                        'product_id' => $item->product_id,
                        'store_id' => $storeId,
                        'batch_number' => $itemData['batch_number'] ?? ('BATCH-' . now()->format('YmdHis')),
                        'expiry_date' => $itemData['expiry_date'] ?? now()->addYear(),
                        'quantity' => $receivedQty,
                        'is_active' => true,
                        'cost_price' => $item->unit_cost ?? 0,
                    ]);

                    // Create transaction record
                    StockTransaction::create([
                        'store_id' => $storeId,
                        'product_id' => $item->product_id,
                        'quantity_change' => $receivedQty,
                        'type' => 'receipt',
                        'reference_type' => 'stock_request',
                        'reference_id' => $stockRequest->id,
                        'remarks' => "Received from PO: {$stockRequest->request_number}. Batch: " . ($itemData['batch_number'] ?? 'N/A')
                    ]);
                }
            }

            $stockRequest->update([
                'status' => 'completed',
                'verified_at' => now(),
                'store_remarks' => $request->remarks ? ($stockRequest->store_remarks . "\n\nReceipt Remarks: " . $request->remarks) : $stockRequest->store_remarks
            ]);
        });

        return redirect()->route('store.stock-control.requests')
            ->with('success', 'Stock received and inventory updated successfully');
    }

    /**
     * Estimate required pallets for the given items based on warehouse rules
     * Expects payload: { items: [{ product_id, quantity }, ...] }
     */
    public function estimatePallets(Request $request, \App\Services\PalletizationService $service)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $itemsToPack = [];
        $totalWeight = 0;

        foreach ($request->items as $itemData) {
            $product = \App\Models\Product::find($itemData['product_id']);
            if ($product) {
                // Mock a PO item struct to pass into the service
                $mockItem = new \stdClass();
                $mockItem->product = $product;
                $mockItem->quantity = $itemData['quantity'];

                $itemsToPack[] = $mockItem;
            }
        }

        try {
            $pallets = $service->calculateOptimalArrangement($itemsToPack);

            foreach ($pallets as $p) {
                $totalWeight += $p['total_weight'];
            }

            return response()->json([
                'success' => true,
                'total_pallets' => count($pallets),
                'total_weight' => $totalWeight,
                'pallets' => $pallets
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to estimate pallets: ' . $e->getMessage()
            ], 500);
        }
    }
}
