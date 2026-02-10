<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StoreStock;
use App\Models\StockAudit;
use App\Models\StockAuditItem;
use App\Models\StockTransaction;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StoreAuditController extends Controller
{
    // 1. List Past Audits
    public function index()
    {
        $audits = StockAudit::where('store_id', Auth::user()->store_id)
            ->latest()
            ->paginate(10);
            
        return view('store.audits.index', compact('audits'));
    }

    // 2. Show "Start Audit" Form
    public function create()
    {
        $categories = ProductCategory::all();
        return view('store.audits.create', compact('categories'));
    }

    // 3. Generate Audit Sheet (Draft)
    public function store(Request $request)
    {
        $storeId = Auth::user()->store_id;
        
        // Filter products (e.g., specific category or full store)
        $query = StoreStock::where('store_id', $storeId)->with('product');
        
        if ($request->category_id) {
            $query->whereHas('product', function($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }

        $stocks = $query->get();

        if ($stocks->isEmpty()) {
            return back()->with('error', 'No products found to audit.');
        }

        DB::beginTransaction();
        try {
            // Create Audit Header
            $audit = StockAudit::create([
                'audit_number' => 'AUD-' . strtoupper(Str::random(10)),
                'store_id' => $storeId,
                'status' => 'in_progress',
                'initiated_by' => Auth::id(),
                'notes' => $request->notes
            ]);

            // Create Audit Items (Snapshot of System Stock)
            foreach ($stocks as $stock) {
                StockAuditItem::create([
                    'stock_audit_id' => $audit->id,
                    'product_id' => $stock->product_id,
                    'system_qty' => $stock->quantity,
                    'physical_qty' => null, // To be filled by user
                    'variance_qty' => 0,
                    'cost_price' => $stock->product->cost_price ?? 0
                ]);
            }

            DB::commit();
            return redirect()->route('store.audits.show', $audit->id);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    // 4. Audit Interface (Enter Counts)
    public function show($id)
    {
        $audit = StockAudit::where('store_id', Auth::user()->store_id)
            ->with(['items.product'])
            ->findOrFail($id);

        return view('store.audits.show', compact('audit'));
    }

    // 5. Save Counts & Finalize
    public function update(Request $request, $id)
    {
        $audit = StockAudit::findOrFail($id);
        
        if ($audit->status == 'completed') {
            return back()->with('error', 'Audit is already finalized.');
        }

        $data = $request->input('items'); // Array of [item_id => physical_qty]

        DB::beginTransaction();
        try {
            foreach ($data as $itemId => $qty) {
                $item = StockAuditItem::where('id', $itemId)->where('stock_audit_id', $audit->id)->first();
                if ($item) {
                    $physical = (int)$qty;
                    $variance = $physical - $item->system_qty;

                    $item->update([
                        'physical_qty' => $physical,
                        'variance_qty' => $variance
                    ]);

                    // AUTO-ADJUST STOCK (Optional: Only if user clicks "Finalize")
                    if ($request->has('finalize') && $variance != 0) {
                        // 1. Update Store Stock
                        $stock = StoreStock::where('store_id', $audit->store_id)
                            ->where('product_id', $item->product_id)
                            ->first();
                        
                        if ($stock) {
                            $stock->quantity = $physical;
                            $stock->save();
                        }

                        // 2. Log Transaction
                        StockTransaction::create([
                            'store_id' => $audit->store_id,
                            'product_id' => $item->product_id,
                            'type' => 'adjustment',
                            'quantity_change' => $variance,
                            'running_balance' => $physical,
                            'reference_id' => $audit->audit_number,
                            'remarks' => 'Audit Variance Adjustment',
                            'ware_user_id' => null
                        ]);
                    }
                }
            }

            if ($request->has('finalize')) {
                $audit->update(['status' => 'completed', 'completed_at' => now()]);
                $message = 'Audit Finalized & Stock Adjusted!';
            } else {
                $message = 'Progress Saved.';
            }

            DB::commit();
            return redirect()->route('store.audits.index')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}