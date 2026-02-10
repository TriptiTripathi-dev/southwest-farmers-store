<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockAudit extends Model
{
    protected $guarded = [];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(StockAuditItem::class);
    }

    // Note: In the Store Panel, this usually refers to the StoreUser who started it.
    // Ensure your database allows storing StoreUser IDs here.
    public function initiator()
    {
        return $this->belongsTo(StoreUser::class, 'initiated_by');
    }
    
    public function store()
    {
        return $this->belongsTo(StoreDetail::class);
    }
}