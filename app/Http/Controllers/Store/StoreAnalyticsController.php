<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SaleItem;
use App\Models\SaleReturn;
use App\Models\ProductBatch;
use App\Models\StockTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StoreAnalyticsController extends Controller
{
    public function index()
    {
        $storeId = Auth::user()->store_id;
        $today = Carbon::today();
        $twoWeeksLater = Carbon::today()->addDays(14);

        // 1. Sales by Department (Top 5)
        $deptSales = SaleItem::whereHas('sale', function($q) use ($storeId) {
                $q->where('store_id', $storeId);
            })
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('departments', 'products.department_id', '=', 'departments.id')
            ->select('departments.name', DB::raw('SUM(sale_items.total) as total_sales'))
            ->groupBy('departments.name')
            ->orderByDesc('total_sales')
            ->take(5)
            ->get();

        // 2. Expiry Risk (Next 14 Days)
        // Note: Assuming ProductBatch has store_id or we filter by warehouse batches allocated to store.
        // Based on your schema, StoreStock doesn't have dates, so we check Batches linked to this store.
        $expiringBatches = ProductBatch::where('store_id', $storeId)
            ->where('quantity', '>', 0)
            ->whereBetween('expiry_date', [$today, $twoWeeksLater])
            ->with('product')
            ->orderBy('expiry_date')
            ->get();

        // 3. Waste vs Sales Ratio (This Month)
        $startOfMonth = Carbon::now()->startOfMonth();
        
        $totalSales = SaleItem::whereHas('sale', function($q) use ($storeId, $startOfMonth) {
                $q->where('store_id', $storeId)->where('created_at', '>=', $startOfMonth);
            })->sum('total');

        // Waste = Transactions with type 'damage', 'spoilage', 'expired'
        $totalWaste = StockTransaction::where('store_id', $storeId)
            ->whereIn('type', ['damage', 'spoilage', 'expired'])
            ->where('created_at', '>=', $startOfMonth)
            ->sum(DB::raw('ABS(quantity_change) * running_balance')); 
            // Note: Ideally cost_price should be logged in transaction or fetched from product. 
            // Approximating waste value isn't perfect without historical cost, 
            // for now, we'll just count Quantity for the ratio or use current Product Price.
        
        // Let's grab Waste Value using current Product Cost (Approximation)
        $wasteValue = StockTransaction::where('stock_transactions.store_id', $storeId)
            ->whereIn('type', ['damage', 'spoilage', 'expired'])
            ->where('stock_transactions.created_at', '>=', $startOfMonth)
            ->join('products', 'stock_transactions.product_id', '=', 'products.id')
            ->sum(DB::raw('ABS(stock_transactions.quantity_change) * products.cost_price'));

        $wasteRatio = $totalSales > 0 ? ($wasteValue / $totalSales) * 100 : 0;

        // 4. Recent Refunds
        $recentReturns = SaleReturn::where('store_id', $storeId)
            ->with(['sale', 'customer'])
            ->latest()
            ->take(5)
            ->get();

        return view('store.analytics.index', compact(
            'deptSales', 
            'expiringBatches', 
            'totalSales', 
            'wasteValue', 
            'wasteRatio',
            'recentReturns'
        ));
    }
}