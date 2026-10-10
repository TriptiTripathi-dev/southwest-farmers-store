<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** This person may clock in/out at this other store (kitchen spec 9.3). */
class CrossLocationPermission extends Model
{
    protected $fillable = ['store_user_id', 'store_id', 'granted_by_name'];

    public function employee()
    {
        return $this->belongsTo(StoreUser::class, 'store_user_id');
    }

    public function store()
    {
        return $this->belongsTo(StoreDetail::class, 'store_id');
    }
}
