<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * This is the single biggest fix in this pass: SoftDeletes was declared here,
 * but store_purchase_orders (created by warehouse-pos's own migration, and
 * matched by warehouse-pos's own StorePurchaseOrder model — no SoftDeletes)
 * has no deleted_at column at all. Every Eloquent query through this model —
 * list, view, create, receive — automatically added a `deleted_at is null`
 * filter and crashed with "column does not exist". This was the entire
 * Store-side "Warehouse Orders (PO)" section, not one broken screen.
 */
class StorePurchaseOrder extends Model
{
    use HasFactory;

    // Reconciled against the actual live columns — several of these (total_items,
    // requested_by) were never real columns at all (writes to them crashed;
    // total_items was at least read-only so it just silently fell back to 1).
    // total_amount/dispatched_at/received_at/warehouse_remarks/store_remarks
    // are real now (added by the migration alongside this fix) — they were
    // referenced throughout the controller/views but didn't exist before.
    protected $fillable = [
        'po_number',
        'store_id',
        'request_date',
        'status',
        'admin_note',
        'created_by',
        'approved_by',
        'approved_at',
        'total_amount',
        'dispatched_at',
        'received_at',
        'warehouse_remarks',
        'store_remarks',
    ];

    protected $casts = [
        'request_date' => 'date',
        'approved_at' => 'datetime',
        'dispatched_at' => 'datetime',
        'received_at' => 'datetime',
        'total_amount' => 'decimal:2',
    ];

    public function items()
    {
        // Default convention assumes store_purchase_order_id; the real
        // column (matching warehouse-pos's own item-creation code) is
        // store_po_id — every show()/getOrders() call crashed on this.
        return $this->hasMany(StorePurchaseOrderItem::class, 'store_po_id');
    }

    public function store()
    {
        return $this->belongsTo(StoreDetail::class, 'store_id');
    }

    public function user()
    {
        // requested_by isn't a real column (see $fillable note above) —
        // the actual "who created this" column is created_by.
        return $this->belongsTo(StoreUser::class, 'created_by');
    }

    public static function generatePONumber($storeId)
    {
        $store = StoreDetail::find($storeId);
        $storeCode = 'STORE';
        if ($store && $store->store_name) {
            $name = preg_replace('/^SWF\s*-\s*/i', '', $store->store_name);
            $cleanName = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $name));
            $storeCode = strlen($cleanName) >= 3 ? substr($cleanName, 0, 4) : $cleanName;
        }
        $year = date('Y');
        $prefix = "REQ-" . $storeCode . "-" . $year;

        $count = self::where('store_id', $storeId)
            ->whereYear('created_at', date('Y'))
            ->count() + 1;

        return $prefix . "-" . str_pad($count, 2, '0', STR_PAD_LEFT);
    }

    public function calculateTotals()
    {
        $this->total_items = $this->items()->count();
        $this->total_amount = $this->items()->sum('total_cost');
        $this->save();
    }
}
