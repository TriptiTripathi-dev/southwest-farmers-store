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
            ->addColumn('sku', fn($row) => $row->product->sku ?? '-')
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
            ->addColumn('sku', fn($row) => $row->product->sku ?? '-')
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
        StockRequest::create([
            'store_id' => $user->store_id,
            'product_id' => $request->product_id,
            'requested_quantity' => $request->quantity,
            'status' => 'pending',
            'reason' => 'low_stock',
            'remarks' => 'Quick request from low stock alert',
        ]);

        return response()->json(['success' => true, 'message' => 'Stock request sent successfully']);
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
        $productsList = Product::whereHas('storeStocks', function($q) use ($storeId) {
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
                'products.sku',
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
            if ($daysLeft <= 15) return '<span class="badge bg-danger">Critical</span>';
            if ($daysLeft <= 30) return '<span class="badge bg-warning">Urgent</span>';
            return '<span class="badge bg-info">Warning</span>';
        })
        ->addColumn('action', fn($row) => '<button class="btn btn-sm btn-warning">Request Recall</button>')
        ->rawColumns(['status', 'action'])
        ->make(true);
}

    public function requests()
    {
        $user = Auth::user();
        $storeId = $user->store_id;
        $requests = StockRequest::where('store_id', $storeId)->with('product')->latest()->paginate(15);
        return view('store.stock-control.requests', compact('requests'));
    }

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
        $pending = StockRequest::where('store_id', $storeId)->where('status', 'dispatched')->with('product')->paginate(10);
        return view('store.stock-control.received', compact('pending'));
    }

    public function confirmReceived(Request $request, $id)
    {
        $request->validate([
            'received_quantity' => 'required|integer|min:1',
        ]);

        $user = Auth::user();
        $storeId = $user->store_id;

        $stockRequest = StockRequest::where('store_id', $storeId)->where('status', 'dispatched')->findOrFail($id);

        DB::transaction(function () use ($request, $stockRequest, $storeId) {
            $stock = StoreStock::firstOrCreate(['store_id' => $storeId, 'product_id' => $stockRequest->product_id]);
            $stock->increment('quantity', $request->received_quantity);

            $stockRequest->update([
                'received_quantity' => $request->received_quantity,
                'status' => 'completed',
            ]);
        });

        return redirect()->route('store.stock-control.received')->with('success', 'Received confirmed');
    }
}
