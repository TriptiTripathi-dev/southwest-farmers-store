<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockAuditItem extends Model
{
    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function audit()
    {
        return $this->belongsTo(StockAudit::class, 'stock_audit_id');
    }
}