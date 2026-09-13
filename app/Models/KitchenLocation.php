<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KitchenLocation extends Model
{
    protected $fillable = [
        'store_id',
        'parent_id',
        'name',
        'type',
        'description',
    ];

    public function store()
    {
        return $this->belongsTo(StoreDetail::class, 'store_id');
    }

    public function stocks()
    {
        return $this->hasMany(KitchenStock::class);
    }
}
