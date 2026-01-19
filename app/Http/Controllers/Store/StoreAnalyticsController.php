<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StoreStock;
use App\Models\StockAdjustment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StoreAnalyticsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $storeId = $user->store_id ?? $user->id;

        // 1. Total Inventory Value (Quantity * Selling Price)
        $totalStockValue = StoreStock::where('store_id', $storeId)
            ->sum(DB::raw('quantity * selling_price'));

        // 2. Low Stock Products (Less than 10 qty)
        $lowStockProducts = StoreStock::where('store_id', $storeId)
            ->where('quantity', '<', 10)
            ->with('product')
            ->orderBy('quantity', 'asc')
            ->limit(5)
            ->get();

        // 3. Category Distribution (Pie Chart)
        // Groups inventory by Category Name
        $categoryData = StoreStock::where('store_stocks.store_id', $storeId)
            ->join('products', 'store_stocks.product_id', '=', 'products.id')
            ->join('product_categories', 'products.category_id', '=', 'product_categories.id')
            ->select('product_categories.name', DB::raw('SUM(store_stocks.quantity) as total_qty'))
            ->groupBy('product_categories.name')
            ->get();

        // 4. Usage/Sales Trend (Last 30 Days)
        // Uses StockAdjustment 'subtract' operations to track consumption
        $trendData = StockAdjustment::where('store_id', $storeId)
            ->where('operation', 'subtract')
            ->where('created_at', '>=', now()->subDays(30))
            ->select(
                DB::raw("TO_CHAR(created_at, 'YYYY-MM-DD') as date"), 
                DB::raw('SUM(quantity) as total_consumed')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Prepare Data for Charts
        $catLabels = $categoryData->pluck('name');
        $catValues = $categoryData->pluck('total_qty');
        
        $trendLabels = $trendData->pluck('date');
        $trendValues = $trendData->pluck('total_consumed');

        return view('store.analytics.index', compact(
            'totalStockValue', 
            'lowStockProducts', 
            'catLabels', 'catValues', 
            'trendLabels', 'trendValues'
        ));
    }
}