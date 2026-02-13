<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Sale;
use App\Models\StoreStock;
use App\Models\StockRequest;
use App\Models\SaleItem;

class StoreDashboardController extends Controller
{
    public function index(Request $request)
    {
        set_time_limit(300);
        $user = auth()->user();
        $storeId = $user->store_id;

        // 1. Date Filter Setup
        $end = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();
        $start = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->subDays(30)->startOfDay();

        $data = [];

        // --- 2. KPI METRICS ---

        // A. Sales & Revenue (Requires 'view_sales_report' or 'view_monthly_sales')
        if ($user->hasPermission('view_sales_report')) {
            $data['total_revenue'] = Sale::where('store_id', $storeId)
                ->whereBetween('created_at', [$start, $end])
                ->sum('total_amount');

            $data['total_orders'] = Sale::where('store_id', $storeId)
                ->whereBetween('created_at', [$start, $end])
                ->count();
            
            // Calculate Growth % (vs previous period)
            $prevStart = $start->copy()->subDays($start->diffInDays($end));
            $prevRevenue = Sale::where('store_id', $storeId)
                ->whereBetween('created_at', [$prevStart, $start->copy()->subSecond()])
                ->sum('total_amount');
            
            $data['revenue_growth'] = $this->calculateTrend($data['total_revenue'], $prevRevenue);
        }

        // B. Inventory Health (Requires 'adjust_stock')
        if ($user->hasPermission('adjust_stock')) {
            $data['low_stock_count'] = StoreStock::where('store_id', $storeId)
                ->where('quantity', '<', 10) // Threshold can be dynamic
                ->count();
            
            $data['total_items'] = StoreStock::where('store_id', $storeId)
                ->where('quantity', '>', 0)
                ->count();
        }

        // C. Pending Requests (Requires 'request_stock')
        if ($user->hasPermission('request_stock')) {
            $data['pending_requests'] = StockRequest::where('store_id', $storeId)
                ->where('status', 'pending')
                ->count();
        }

        // --- 3. CHARTS DATA ---

        // Chart A: Sales Trend (Area Chart)
        if ($user->hasPermission('view_sales_report')) {
            $salesTrend = Sale::where('store_id', $storeId)
                ->whereBetween('created_at', [$start, $end])
                ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            $data['chart_dates'] = $salesTrend->pluck('date');
            $data['chart_sales'] = $salesTrend->pluck('total');
        }

        // Chart B: Top Selling Products (Donut/Bar)
        if ($user->hasPermission('view_reports') || $user->hasPermission('view_sales_report')) {
            $topProducts = SaleItem::join('sales', 'sale_items.sale_id', '=', 'sales.id')
                ->join('products', 'sale_items.product_id', '=', 'products.id')
                ->where('sales.store_id', $storeId)
                ->whereBetween('sales.created_at', [$start, $end])
                ->select('products.product_name', DB::raw('SUM(sale_items.quantity) as qty'))
                ->groupBy('products.product_name')
                ->orderByDesc('qty')
                ->limit(5)
                ->get();

            $data['top_products'] = $topProducts;
        }

        // --- 4. RECENT ACTIVITY ---
        
        // Recent Orders (Requires 'view_orders')
        if ($user->hasPermission('view_orders')) {
            $data['recent_orders'] = Sale::where('store_id', $storeId)
                ->with('customer')
                ->latest()
                ->limit(6)
                ->get();
        }

        return view('dashboard', compact('data', 'start', 'end'));
    }

    /**
     * Calculate percentage trend.
     */
    private function calculateTrend($current, $previous)
    {
        if ($previous == 0) return $current > 0 ? 100 : 0;
        return round((($current - $previous) / $previous) * 100, 1);
    }
}