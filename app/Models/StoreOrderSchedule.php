<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreOrderSchedule extends Model
{
    protected $fillable = [
        'store_id',
        'expected_day'
    ];

    public function store()
    {
        return $this->belongsTo(StoreDetail::class, 'store_id');
    }
}
