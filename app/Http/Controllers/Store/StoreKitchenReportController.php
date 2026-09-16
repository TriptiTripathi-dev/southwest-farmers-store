<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\SaleItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * The Warehouse version of Sales Ranking used rand(10, 500) mock data
 * ("POS side isn't fully integrated into this table yet"), so this builds
 * a real report off actual sale_items/sales instead of porting the mock.
 */
class StoreKitchenReportController extends Controller
{
    public function salesRanking()
    {
        $storeId = Auth::user()->store_id;

        $sold = SaleItem::query()
            ->whereNotNull('menu_item_id')
            ->whereHas('sale', fn ($q) => $q->where('store_id', $storeId))
            ->select('menu_item_id', DB::raw('SUM(quantity) as total_quantity_sold'), DB::raw('SUM(total) as total_revenue'))
            ->groupBy('menu_item_id')
            ->get()
            ->keyBy('menu_item_id');

        $rankedItems = MenuItem::where('store_id', $storeId)
            ->with('category')
            ->get()
            ->map(function ($item) use ($sold) {
                $stats = $sold->get($item->id);
                return (object) [
                    'name' => $item->name,
                    'categoryName' => $item->category->name ?? 'N/A',
                    'price' => $item->price,
                    'total_quantity_sold' => $stats->total_quantity_sold ?? 0,
                    'total_revenue' => $stats->total_revenue ?? 0,
                ];
            })
            ->sortByDesc('total_revenue')
            ->values();

        return view('store.kitchen.reports.sales-ranking', compact('rankedItems'));
    }
}
