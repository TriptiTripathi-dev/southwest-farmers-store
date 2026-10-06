<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Store shelf -> store kitchen request (kitchen spec 7.1): pending until an
 * approver decides; stock moves only on approval.
 */
class KitchenTransferRequest extends Model
{
    public const PENDING = 'pending';
    public const APPROVED = 'approved';
    public const DENIED = 'denied';
    public const CANCELLED = 'cancelled';

    public const STATUS_LABELS = [
        self::PENDING => 'Pending Area Manager Approval',
        self::APPROVED => 'Approved',
        self::DENIED => 'Denied',
        self::CANCELLED => 'Cancelled',
    ];

    protected $fillable = [
        'store_id', 'kitchen_location_id', 'status', 'notes',
        'requested_by', 'requested_by_name', 'decided_by', 'decided_by_name', 'decided_at', 'decision_note',
    ];

    protected $casts = ['decided_at' => 'datetime'];

    public function items()
    {
        return $this->hasMany(KitchenTransferRequestItem::class);
    }

    public function store()
    {
        return $this->belongsTo(StoreDetail::class, 'store_id');
    }

    public function statusLabel(): string
    {
        return self::STATUS_LABELS[$this->status] ?? ucfirst($this->status);
    }

    public function number(): string
    {
        return 'KTR-' . str_pad((string) $this->id, 5, '0', STR_PAD_LEFT);
    }
}
