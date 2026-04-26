<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'request_number',
        'department_id',
        'reviewed',
        'reviewed_by',
        'reviewed_at',
        'gm_email',
        'gm_phone',
        'vp_email',
        'vp_phone',
        'received_by_name',
        'receiving_progress',
        'received_at',
        'received_qty',
        'total_items',
        'total_amount',
        'requested_by',
        'approved_by',
        'approved_at',
        'status',
        'admin_note',
        'store_payment_proof',
        'store_remarks',
        'warehouse_payment_proof',
        'warehouse_remarks',
        'verified_at',
        'purchase_ref',
        // Old fields (kept for backward compatibility during migration)
        'product_id',
        'requested_quantity',
        'fulfilled_quantity'
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'approved_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'received_at' => 'datetime',
        'total_amount' => 'decimal:2',
        'reviewed' => 'boolean'
    ];

    // Constants for Status
    const STATUS_PENDING = 'pending';
    const STATUS_AWAITING_APPROVAL = 'awaiting_approval'; // New status requested
    const STATUS_DISPATCHED = 'dispatched';
    const STATUS_COMPLETED = 'completed';
    const STATUS_REJECTED = 'rejected';

    // Relationships
    public function items()
    {
        return $this->hasMany(StockRequestItem::class);
    }

    public function store()
    {
        return $this->belongsTo(StoreDetail::class, 'store_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function requestedBy()
    {
        return $this->belongsTo(StoreUser::class, 'requested_by');
    }

    public function reviewedBy()
    {
        return $this->belongsTo(StoreUser::class, 'reviewed_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(StoreUser::class, 'approved_by');
    }

    // Old relationship (kept for backward compatibility)
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Generate unique request number
    public static function generateRequestNumber($storeId)
    {
        $year = date('Y');
        $lastRequest = self::where('store_id', $storeId)
            ->where('request_number', 'LIKE', "REQ-{$year}-%")
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $lastRequest 
            ? intval(substr($lastRequest->request_number, -3)) + 1 
            : 1;
        
        return sprintf("REQ-%s-%03d", $year, $sequence);
    }

    // Calculate totals from items
    public function calculateTotals()
    {
        $this->total_items = $this->items()->count();
        $this->total_amount = $this->items()->sum('total_cost');
        $this->save();
    }

    // Helper to get pending amount (backward compatibility)
    public function getPendingQuantityAttribute()
    {
        return max(0, $this->requested_quantity - ($this->fulfilled_quantity ?? 0));
    }
}