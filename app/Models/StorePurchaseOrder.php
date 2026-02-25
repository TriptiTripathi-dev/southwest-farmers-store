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
        $prefix = "PO-" . date('Ymd');
        $lastOrder = self::where('store_id', $storeId)
            ->where('po_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        $sequence = 1;
        if ($lastOrder) {
            $lastSequence = (int) substr($lastOrder->po_number, -4);
            $sequence = $lastSequence + 1;
        }

        return $prefix . "-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    public function calculateTotals()
    {
        $this->total_items = $this->items()->count();
        $this->total_amount = $this->items()->sum('total_cost');
        $this->save();
    }
}
