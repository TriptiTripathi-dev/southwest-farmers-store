<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Kitchen inventory ledger (kitchen spec section 6): one row per movement,
 * written only by KitchenInventoryService and never edited, so every kitchen
 * quantity can be explained.
 */
class KitchenStockTransaction extends Model
{
    public const UPDATED_AT = null;

    /** type => label shown to users */
    public const TYPES = [
        'opening' => 'Opening balance',
        'transfer_in' => 'Transfer from store',
        'receive' => 'Received',
        'use' => 'Used',
        'adjust_in' => 'Adjustment (+)',
        'adjust_out' => 'Adjustment (-)',
        'waste' => 'Waste',
        'count' => 'Physical count',
    ];

    protected $fillable = [
        'kitchen_location_id', 'kitchen_stock_id', 'type', 'quantity_change', 'balance_after',
        'reason', 'notes', 'user_id', 'user_name',
    ];

    protected $casts = [
        'quantity_change' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    public function stock()
    {
        return $this->belongsTo(KitchenStock::class, 'kitchen_stock_id');
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->type] ?? ucfirst(str_replace('_', ' ', $this->type));
    }
}
