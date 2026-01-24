<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecallRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'recall_requests';

    protected $fillable = [
        'store_id',
        'product_id',
        'requested_quantity',
        'approved_quantity',
        'dispatched_quantity',
        'received_quantity',
        'status',
        'reason',
        'reason_remarks',
        'store_remarks',
        'warehouse_remarks',
        'initiated_by',
        'approved_by_store_user_id',
        'received_by_ware_user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'requested_quantity' => 'integer',
        'approved_quantity' => 'integer',
        'dispatched_quantity' => 'integer',
        'received_quantity' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // --- Relationships ---

    public function store()
    {
        return $this->belongsTo(StoreDetail::class, 'store_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * The Store User who initiated the request.
     */
    public function initiator()
    {
        return $this->belongsTo(StoreUser::class, 'initiated_by');
    }

    /**
     * The Store User (Manager) who approved the dispatch.
     */
    public function storeApprover()
    {
        return $this->belongsTo(StoreUser::class, 'approved_by_store_user_id');
    }

    /**
     * The Warehouse User who received the stock.
     */
    public function warehouseReceiver()
    {
        return $this->belongsTo(WareUser::class, 'received_by_ware_user_id');
    }
}