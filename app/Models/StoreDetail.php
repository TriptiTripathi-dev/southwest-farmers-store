<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreDetail extends Model
{
    use SoftDeletes;

    protected $table = 'store_details';

    protected $fillable = [
        'warehouse_id',
        'store_name',
        'store_code',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'country',
        'pincode',
        'latitude',
        'longitude',
        'is_active',
        'store_user_id'
    ];
     public function user()
    {
        return $this->belongsTo(StoreUser::class, 'store_user_id');
    }
}