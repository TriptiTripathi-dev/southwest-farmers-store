<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\StoreStock;
use App\Models\ProductCategory;
use Carbon\Carbon;
use App\Models\StockRequest;
use App\Models\ProductBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;

class StoreStockControlController extends Controller
{
    public function overview()
    {
        $categories = ProductCategory::where('is_active', true)->get();
        return view('store.stock-control.overview', compact('categories'));
    }

    public function overviewData(Request $request)
    {
        $user = Auth::user();
        $storeId = $user->store_id;

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
            ->addColumn('value', fn($row) => number_format($row->quantity * ($row->product->cost_price ?? 0), 2))
         
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

    public function valuation()
    {
        $user = Auth::user();
        $storeId = $user->store_id;

        // 1. Current Total Store Value (fixed: use products.cost_price)
        $storeValue = StoreStock::where('store_stocks.store_id', $storeId)
            ->join('products', 'store_stocks.product_id', '=', 'products.id')
            ->sum(DB::raw('store_stocks.quantity * products.cost_price'));

        // 2. Top 10 Products by Value (fixed ambiguity & correct column)
        $topProducts = StoreStock::where('store_stocks.store_id', $storeId)
            ->join('products', 'store_stocks.product_id', '=', 'products.id')
            ->select([
                'products.product_name',
                'products.sku',
                'store_stocks.quantity',
                DB::raw('store_stocks.quantity * products.cost_price as value')
            ])
            ->orderByDesc('value')
            ->limit(10)
            ->get();

        // 3. 30-Day Valuation Trend (real data from stock_transactions)
        $trend = DB::table('stock_transactions')
            ->select(DB::raw('DATE(created_at) as date'))
            ->selectRaw('SUM(quantity_change * running_balance) as daily_change')
            ->where('store_id', $storeId)
            ->where('created_at', '>=', Carbon::today()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $dates = [];
        $trendData = [];
        $cumulative = $storeValue;

        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i)->format('Y-m-d');
            $dates[] = $date;

            $dayChange = $trend->firstWhere('date', $date)->daily_change ?? 0;
            $cumulative -= $dayChange; // backward to show historical value
            $trendData[] = max(0, round($cumulative, 2));
        }

        $dates = array_reverse($dates);
        $trendData = array_reverse($trendData);

        return view('store.stock-control.valuation', compact(
            'storeValue',
            'topProducts',
            'dates',
            'trendData'
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
