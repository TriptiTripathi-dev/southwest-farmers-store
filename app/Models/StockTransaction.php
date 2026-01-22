<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'warehouse_id',
        'product_batch_id',
        'ware_user_id',
        'store_id',
        'type',
        'quantity_change',
        'running_balance',
        'reference_id',
        'remarks',
    ];

    // Relationships

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function productBatch()
    {
        return $this->belongsTo(ProductBatch::class);
    }

    // Linked to the Warehouse User who performed the action
    public function wareUser()
    {
        return $this->belongsTo(WareUser::class, 'ware_user_id');
    }

    // Optional: If the transaction involves a specific store
    public function store()
    {
        return $this->belongsTo(StoreDetail::class, 'store_id');
    }
}