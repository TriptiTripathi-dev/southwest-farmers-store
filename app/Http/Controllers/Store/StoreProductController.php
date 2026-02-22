<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductSubcategory;
use App\Models\Department; // <--- YE IMPORT KAREIN
use App\Models\StoreStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StoreProductExport;
use App\Imports\StoreProductImport;
use App\Models\StockAdjustment;
use App\Models\StockTransaction;
use App\Models\StoreNotification;
use App\Models\ProductBatch;
use App\Models\StockRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class StoreProductController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $storeId = $user->store_id ?? $user->id;

        // Department ko bhi load kiya hai (with 'department')
        $query = Product::with(['category', 'subcategory', 'storeStocks', 'department']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('product_name', 'ilike', "%{$search}%")
                    ->orWhere('sku', 'ilike', "%{$search}%")
                    ->orWhere('barcode', 'ilike', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $isActive = $request->status === 'active' ? 1 : 0;
            $query->where('is_active', $isActive);
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $products = $query->latest()->paginate(10)->withQueryString();
        $categories = ProductCategory::select('id', 'name')->get();

        return view('store.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $user = Auth::user();
        $storeId = $user->store_id ?? $user->id;

        $categories = ProductCategory::whereNull('store_id')
            ->orWhere('store_id', $storeId)
            ->where('is_active', true)
            ->get();

        // <--- DEPARTMENTS FETCH KIYE
        $departments = Department::where('is_active', true)->orderBy('name')->get();

        return view('store.products.create', compact('categories', 'departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string|max:255',
            'sku' => 'required|unique:products,sku',
            'barcode' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id', // <--- VALIDATION ADD KI
            'category_id' => 'required',
            'selling_price' => 'required|numeric',
        ]);

        $user = Auth::user();
        $storeId = $user->store_id ?? $user->id;

        $product = Product::create([
            'store_id' => $storeId,
            'department_id' => $request->department_id, // <--- SAVE KIYA
            'category_id' => $request->category_id,
            'subcategory_id' => $request->subcategory_id,
            'product_name' => $request->product_name,
            'sku' => $request->sku,
            'barcode' => $request->barcode,
            'unit' => $request->unit ?? 'pcs',
            'price' => $request->selling_price,
            'icon' => $request->hasFile('image') ? $request->file('image')->store('products', 'public') : null,
            'is_active' => true
        ]);

        StoreStock::create([
            'store_id' => $storeId,
            'product_id' => $product->id,
            'quantity' => 0,
            'selling_price' => $request->selling_price
        ]);

        StoreNotification::create([
            'user_id' => Auth::id(),
            'store_id' => Auth::user()->store_id,
            'title' => 'Product Added',
            'message' => "New product '{$request->product_name}' added to inventory.",
            'type' => 'success',
            'url' => route('store.products.index'),
        ]);

        return redirect()->route('store.products.index')->with('success', 'Product created successfully.');
    }

    public function edit($id)
    {
        $product = Product::where('id', $id)->firstOrFail();
        $categories = ProductCategory::all();
        
        // <--- EDIT KE LIYE BHI DEPARTMENTS CHAHIYE
        $departments = Department::where('is_active', true)->orderBy('name')->get();

        return view('store.products.edit', compact('product', 'categories', 'departments'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $oldPrice = (float) $product->price;

        if ($product->store_id != null) {
            $request->validate([
                'barcode' => 'required|string|max:255',
            ]);

            $product->update($request->only([
                'product_name',
                'description',
                'department_id', // <--- UPDATE KIYA
                'category_id',
                'subcategory_id',
                'unit',
                'barcode'
            ]));

            if ($request->hasFile('image')) {
                $product->update(['icon' => $request->file('image')->store('products', 'public')]);
            }
        }

        if ($product->store_id != null && $request->has('selling_price')) {
             $newPrice = (float) $request->selling_price;
             $product->update(['price' => $newPrice]);
             StoreStock::where('product_id', $product->id)->update(['selling_price' => $newPrice]);

            if (abs($oldPrice - $newPrice) > 0.0001 && Schema::hasTable('price_history')) {
                DB::table('price_history')->insert([
                    'product_id' => $product->id,
                    'old_price' => $oldPrice,
                    'new_price' => $newPrice,
                    'old_margin' => $product->margin_percent,
                    'new_margin' => $product->margin_percent,
                    'changed_by' => Auth::id(),
                    'changed_at' => now(),
                    'effective_from' => now()->toDateString(),
                    'effective_to' => null,
                    'reason' => 'Manual price update from Store Product edit screen',
                    'change_type' => 'manual',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // [NOTIFICATION]
        StoreNotification::create([
            'user_id' => Auth::id(),
            'store_id' => Auth::user()->store_id,
            'title' => 'Product Updated',
            'message' => "Product '{$product->product_name}' details updated.",
            'type' => 'info',
            'url' => route('store.products.index'),
        ]);

        return redirect()->route('store.products.index')->with('success', 'Product updated successfully.');
    }

    // Baaki methods same rahenge (destroy, import, export, analytics, etc.)
    public function destroy($id) {
        $stock = StoreStock::findOrFail($id);
        $product = $stock->product;
        if ($product->store_id != null) {
            $stock->delete();
            $product->delete();
            return back()->with('success', 'Local product deleted.');
        }
        return back()->with('error', 'Cannot delete Warehouse products.');
    }

    public function updateStatus(Request $request) {
        $product = Product::find($request->id);
        if ($product && $product->store_id != null) {
            $product->update(['is_active' => $request->status]);
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 403);
    }

    public function import(Request $request) {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv',
            'category_id' => 'required',
            'subcategory_id' => 'required'
        ]);
        Excel::import(new StoreProductImport(
            $request->category_id,
            $request->subcategory_id
        ), $request->file('file'));
        return back()->with('success', 'Products imported successfully.');
    }

    public function export() {
        return Excel::download(new StoreProductExport, 'products.xlsx');
    }
    
    // Analytics method ko yahan same rakhna hai jo aapne bheja tha...
    public function analytics(Request $request, $id)
    {
        $user = Auth::user();
        $storeId = $user->store_id ?? $user->id;

        // 1. Fetch Product & Stock safely
        $stock = StoreStock::where('store_id', $storeId)
            ->where('product_id', $id)
            ->with('product')
            ->first();

        // CHECK 1: If stock record doesn't exist for this store
        if (!$stock) {
            return redirect()->route('store.products.index')
                ->with('error', 'Stock data not found. This product might not be assigned to your store yet.');
        }

        // CHECK 2: If the linked product is missing (e.g., soft deleted)
        if (!$stock->product) {
            return redirect()->route('store.products.index')
                ->with('error', 'Product details not found.');
        }

        $product = $stock->product;

        // 2. Filter Setup (Date Range)
        $dateRange = $request->input('date_range');
        if ($dateRange && str_contains($dateRange, ' to ')) {
            $dates = explode(' to ', $dateRange);
            $start = Carbon::parse($dates[0]);
            $end = Carbon::parse($dates[1]);
        } else {
            // Default: Last 30 Days
            $start = now()->subDays(29);
            $end = now();
        }

        // 3. Base Query (Sales Transactions)
        $query = StockTransaction::join('store_customers', 'stock_transactions.customer_id', '=', 'store_customers.id')
            ->where('stock_transactions.store_id', $storeId)
            ->where('stock_transactions.product_id', $id)
            ->where('stock_transactions.type', 'sale')
            ->whereBetween('stock_transactions.created_at', [$start->startOfDay(), $end->endOfDay()]);

        // 4. Apply Location Filter
        if ($request->location) {
            $query->where('store_customers.address', $request->location);
        }

        // 5. Aggregate Data for Charts
        $salesData = (clone $query)
            ->selectRaw('DATE(stock_transactions.created_at) as date, SUM(ABS(quantity_change)) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date');

        // 6. Calculate KPIs
        $totalSold = $salesData->sum();
        $daysCount = $start->diffInDays($end) + 1;
        $avgDaily = $daysCount > 0 ? round($totalSold / $daysCount, 2) : 0;

        // 7. Prepare Chart Arrays
        $period = new \DatePeriod($start, new \DateInterval('P1D'), $end->copy()->addDay());
        $dates = [];
        $usage = [];
        foreach ($period as $dt) {
            $dateStr = $dt->format('Y-m-d');
            $dates[] = $dt->format('d M');
            $usage[] = $salesData->get($dateStr, 0);
        }

        // 8. Get Distinct Locations for Dropdown
        $locations = StockTransaction::join('store_customers', 'stock_transactions.customer_id', '=', 'store_customers.id')
            ->where('stock_transactions.store_id', $storeId)
            ->where('stock_transactions.product_id', $id)
            ->where('stock_transactions.type', 'sale')
            ->whereNotNull('store_customers.address')
            ->distinct()
            ->pluck('store_customers.address')
            ->sort()
            ->values();

        // 9. AJAX Request for Location Stats
        if ($request->has('location_stats')) {
            $locationStats = StockTransaction::join('store_customers', 'stock_transactions.customer_id', '=', 'store_customers.id')
                ->join('store_stocks', function ($join) use ($storeId, $id) {
                    $join->on('stock_transactions.product_id', '=', 'store_stocks.product_id')
                        ->where('store_stocks.store_id', $storeId);
                })
                ->where('stock_transactions.store_id', $storeId)
                ->where('stock_transactions.product_id', $id)
                ->where('stock_transactions.type', 'sale')
                ->whereBetween('stock_transactions.created_at', [$start->startOfDay(), $end->endOfDay()])
                ->whereNotNull('store_customers.address')
                ->selectRaw('
                    store_customers.address as location, 
                    SUM(ABS(stock_transactions.quantity_change)) as total_sold,
                    SUM(ABS(stock_transactions.quantity_change) * store_stocks.selling_price) as revenue
                ')
                ->groupBy('store_customers.address')
                ->orderByDesc('total_sold')
                ->limit(10)
                ->get();

            return response()->json([
                'locations' => $locationStats
            ]);
        }

        return view('store.products.analytics', compact(
            'stock',
            'product',
            'dates',
            'usage',
            'locations',
            'totalSold',
            'avgDaily'
        ));
    }

    public function locationInventory($id)
    {
        $product = Product::findOrFail($id);

        $locationStocks = StoreStock::with('store')
            ->where('product_id', $product->id)
            ->orderBy('store_id')
            ->get();

        $inTransitByStore = StockRequest::where('product_id', $product->id)
            ->where('status', StockRequest::STATUS_DISPATCHED)
            ->select('store_id', DB::raw('SUM(COALESCE(fulfilled_quantity, requested_quantity)) as qty'))
            ->groupBy('store_id')
            ->pluck('qty', 'store_id');

        // Proxy for warehouse quantity: total warehouse batch stock (store_id NULL).
        $warehouseQty = (float) ProductBatch::where('product_id', $product->id)
            ->whereNull('store_id')
            ->sum('quantity');

        return view('store.products.location-inventory', compact(
            'product',
            'locationStocks',
            'inTransitByStore',
            'warehouseQty'
        ));
    }

}
