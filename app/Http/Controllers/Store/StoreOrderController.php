<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\StorePurchaseOrder;
use App\Models\StoreOrderSchedule;
use App\Models\StoreStock;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class StoreOrderController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $schedule = StoreOrderSchedule::where('store_id', $user->store_id)->first();
        return view('store.orders.index', compact('schedule'));
    }

    public function getOrders(Request $request)
    {
        try {
            $user = Auth::user();
            $query = StorePurchaseOrder::query()->with(['user', 'items']);

            if ($user && $user->store_id) {
                $query->where('store_id', $user->store_id);
            }

            if ($request->filled('date')) {
                $query->whereDate('created_at', $request->date);
            }

            if ($request->filled('status_filter')) {
                $query->where('status', $request->status_filter);
            }

            $tab = strtolower($request->get('tab', 'receiving'));
            if ($tab === 'receiving') {
                $query->whereIn(DB::raw('LOWER(status)'), ['approved', 'dispatched', 'in_transit', 'receiving']);
            } else {
                $query->whereIn(DB::raw('LOWER(status)'), ['completed', 'partially_received', 'cancelled']);
            }

            $orders = $query->orderBy('created_at', 'desc');

            return DataTables::of($orders)
                ->editColumn('status', function ($order) {
                    $status = strtolower($order->status ?? 'pending');
                    $class = match ($status) {
                        'pending' => 'bg-warning text-dark',
                        'approved' => 'bg-info text-white',
                        'dispatched', 'in_transit' => 'bg-primary text-white',
                        'receiving' => 'bg-secondary text-white fw-bold',
                        'partially_received' => 'bg-warning text-dark',
                        'completed' => 'bg-success text-white',
                        'cancelled' => 'bg-danger text-white',
                        default => 'bg-secondary text-white'
                    };
                    $label = str_replace('_', ' ', ucfirst($status));
                    return '<span class="badge ' . $class . ' px-3 py-2 fs-12">' . $label . '</span>';
                })
                ->addColumn('qty_ordered', fn($order) => $order->items->sum('quantity') ?: ($order->total_items ?? 1))
                ->addColumn('qty_received', fn($order) => $order->items->sum('received_quantity') ?: ($order->status == 'completed' ? ($order->total_items ?? 1) : 0))
                ->editColumn('total_amount', function ($order) {
                    return '$' . number_format($order->total_amount ?? 0, 2);
                })
                ->editColumn('created_at', function ($order) {
                    return $order->created_at ? $order->created_at->format('d M Y') : '---';
                })
                ->addColumn('received_date', function ($order) {
                    return $order->received_at ? $order->received_at->format('d M Y') : ($order->updated_at ? $order->updated_at->format('d M Y') : '---');
                })
                ->addColumn('action', function ($order) use ($tab) {
                    if ($tab === 'receiving') {
                        return '<a href="' . route('store.orders.receive', $order->id) . '" class="btn btn-sm btn-outline-info rounded-pill px-3 fw-bold shadow-sm">
                                    <i class="mdi mdi-truck-check me-1"></i> Receive
                                </a>';
                    }
                    return '<a href="' . route('store.orders.show', $order->id) . '" class="btn btn-sm btn-outline-primary rounded-pill px-3 me-1 fw-bold">
                                <i class="mdi mdi-eye me-1"></i> View
                            </a>';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        } catch (\Throwable $e) {
            \Log::error('StoreOrder getOrders DataTables Error: ' . $e->getMessage());
            return response()->json([
                'draw' => intval($request->get('draw', 1)),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage()
            ]);
        }
    }

    public function show($id)
    {
        $user = Auth::user();
        $order = StorePurchaseOrder::where('store_id', $user->store_id)
            ->with(['items.product', 'user'])
            ->findOrFail($id);

        return view('store.orders.show', compact('order'));
    }

    public function edit($id)
    {
        $user = Auth::user();
        $order = StorePurchaseOrder::where('store_id', $user->store_id)
            ->where('status', 'pending')
            ->with(['items.product'])
            ->findOrFail($id);

        return view('store.orders.edit', compact('order'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:store_purchase_order_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'edit_reason' => 'required|string|min:5', // Requirement 12.7
        ]);

        $user = Auth::user();
        $order = StorePurchaseOrder::where('store_id', $user->store_id)
            ->where('status', 'pending')
            ->findOrFail($id);

        DB::transaction(function () use ($request, $order, $user) {
            $totalAmount = 0;
            $changes = [];

            foreach ($request->items as $itemData) {
                $item = $order->items()->findOrFail($itemData['id']);
                $oldQty = $item->quantity;
                $newQty = $itemData['quantity'];

                if ($oldQty != $newQty) {
                    $changes[] = "Product {$item->product->product_name}: {$oldQty} -> {$newQty}";
                }

                $item->update(['quantity' => $newQty]);
                $totalAmount += $newQty * ($item->unit_cost ?? 0);
            }

            $order->update([
                'total_amount' => $totalAmount,
                'store_remarks' => $order->store_remarks . "\n[Edit by {$user->name}]: " . $request->edit_reason . "\nChanges: " . implode(', ', $changes)
            ]);
        });

        return redirect()->route('store.orders.show', $id)->with('success', 'Purchase Order updated successfully.');
    }

    public function receive($id)
    {
        $user = Auth::user();
        $order = StorePurchaseOrder::where('store_id', $user->store_id)
            ->whereIn('status', ['dispatched', 'in_transit', 'approved', 'receiving'])
            ->with(['items.product'])
            ->findOrFail($id);

        if ($order->status !== 'receiving') {
            $order->update(['status' => 'receiving']);
        }

        return view('store.orders.receive', compact('order'));
    }

    public function confirmReceive(Request $request, $id)
    {
        $request->validate([
            'driver_phone' => 'required|string|max:20',
            'driver_code'  => 'required|string|max:10',
            'staff_name'   => 'required|string|max:100',
            'clocking_id'  => 'required|string|max:50',
            'items' => 'required|array',
            'items.*.id' => 'required|exists:store_purchase_order_items,id',
            'items.*.received_quantity' => 'required|integer|min:0',
            'remarks' => 'nullable|string',
        ]);

        $user = Auth::user();
        $order = StorePurchaseOrder::where('store_id', $user->store_id)
            ->whereIn('status', ['dispatched', 'in_transit', 'approved'])
            ->findOrFail($id);

        DB::transaction(function () use ($request, $order, $user) {
            foreach ($request->items as $itemData) {
                $item = $order->items()->findOrFail($itemData['id']);
                $receivedQty = $itemData['received_quantity'];

                // Update item
                $item->update([
                    'received_quantity' => $receivedQty,
                ]);

                // Update Store Stock
                $stock = StoreStock::firstOrCreate([
                    'store_id' => $user->store_id,
                    'product_id' => $item->product_id
                ]);
                $oldQuantity = $stock->quantity;
                $stock->increment('quantity', $receivedQty);
                $newQuantity = $oldQuantity + $receivedQty;

                // Create Transaction Log
                \App\Models\StockTransaction::create([
                    'store_id' => $user->store_id,
                    'product_id' => $item->product_id,
                    'type' => 'purchase_order',
                    'quantity_change' => $receivedQty,
                    'running_balance' => $newQuantity,
                    'reference_id' => $order->id,
                    'reference_type' => StorePurchaseOrder::class,
                    'remarks' => $request->remarks ?? "Received from PO #{$order->po_number}",
                    'user_id' => $user->id,
                ]);
            }

            // Update PO Status
            $receivingNote = "Received By: {$request->staff_name} (Clocking ID: {$request->clocking_id}) | Driver Phone: {$request->driver_phone} (Code: {$request->driver_code})";
            $order->update([
                'status' => 'completed',
                'received_at' => now(),
                'store_remarks' => ($request->remarks ? $request->remarks . "\n" : '') . $receivingNote,
            ]);
        });

        return redirect()->route('store.orders.index')->with('success', 'Order items received and inventory updated.');
    }

    public function stockLevels()
    {
        return view('store.inventory.stock-levels');
    }

    public function getStockLevelsData(Request $request)
    {
        $user = Auth::user();
        $stocks = StoreStock::where('store_id', $user->store_id)
            ->with('product');

        return DataTables::of($stocks)
            ->addColumn('product_name', fn($s) => $s->product->product_name)
            ->addColumn('upc', fn($s) => $s->product->upc ?? '-')
            ->addColumn('action', function ($stock) {
                return '<button class="btn btn-sm btn-primary edit-levels" 
                            data-id="' . $stock->id . '" 
                            data-min="' . $stock->min_stock . '" 
                            data-max="' . $stock->max_stock . '">
                            <i class="mdi mdi-pencil"></i> Update
                        </button>';
            })
            ->make(true);
    }

    public function updateStockLevels(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:store_stocks,id',
            'min_stock' => 'required|integer|min:0',
            'max_stock' => 'required|integer|min:0|gte:min_stock',
        ]);

        $user = Auth::user();
        $stock = StoreStock::where('store_id', $user->store_id)->findOrFail($request->id);

        $stock->update([
            'min_stock' => $request->min_stock,
            'max_stock' => $request->max_stock,
        ]);

        return response()->json(['success' => true, 'message' => 'Stock levels updated successfully']);
    }

    public function globalVisibility(Request $request, $product_id = null)
    {
        $products = Product::orderBy('product_name')->get();
        $selectedProduct = $product_id ? Product::with(['warehouseStock', 'storeStocks.store'])->findOrFail($product_id) : null;

        // If searching via AJAX or filter
        if ($request->ajax() && $request->has('product_id')) {
            $p = Product::with(['warehouseStock', 'storeStocks.store'])->find($request->product_id);
            if (!$p) return response()->json(['error' => 'Product not found'], 404);

            $locations = [];
            // Warehouse Stock
            $locations[] = [
                'name' => 'Main Warehouse',
                'type' => 'Warehouse',
                'quantity' => $p->warehouseStock->quantity ?? 0,
                'status' => ($p->warehouseStock->quantity ?? 0) > 0 ? 'In Stock' : 'Out of Stock'
            ];

            // Store Stocks
            foreach ($p->storeStocks as $ss) {
                $locations[] = [
                    'name' => $ss->store ? "SWF - {$ss->store->store_name}" : 'Unknown Store',
                    'type' => 'Store',
                    'quantity' => $ss->quantity,
                    'status' => $ss->quantity > 0 ? 'In Stock' : 'Out of Stock'
                ];
            }

            return response()->json([
                'product' => $p,
                'locations' => $locations
            ]);
        }

        return view('store.inventory.visibility', compact('products', 'selectedProduct'));
    }
}
