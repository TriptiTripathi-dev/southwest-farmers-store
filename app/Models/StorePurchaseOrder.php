<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StorePurchaseOrder extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'po_number',
        'store_id',
        'status',
        'total_items',
        'total_amount',
        'warehouse_remarks',
        'store_remarks',
        'approved_at',
        'dispatched_at',
        'received_at',
        'requested_by'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'dispatched_at' => 'datetime',
        'received_at' => 'datetime',
        'total_amount' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(StorePurchaseOrderItem::class);
    }

    public function store()
    {
        return $this->belongsTo(StoreDetail::class, 'store_id');
    }

    public function user()
    {
        return $this->belongsTo(StoreUser::class, 'requested_by');
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
