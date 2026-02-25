<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\StockRequest;
use App\Models\Product;

class StockRequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_request_id',
        'product_id',
        'quantity',
        'dispatched_quantity',
        'received_quantity',
        'unit_cost',
        'total_cost'
    ];

    protected $casts = [
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'quantity' => 'integer'
    ];

    // Relationships
    public function stockRequest()
    {
        return $this->belongsTo(StockRequest::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Automatically calculate total_cost
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            if ($item->quantity && $item->unit_cost) {
                $item->total_cost = $item->quantity * $item->unit_cost;
            }
        });
    }
}
