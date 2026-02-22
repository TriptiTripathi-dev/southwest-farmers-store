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

    public function setStoreNameAttribute($value)
    {
        $name = trim((string) $value);
        if ($name === '') {
            $this->attributes['store_name'] = $name;
            return;
        }

        $this->attributes['store_name'] = str_starts_with($name, 'SWF - ')
            ? $name
            : 'SWF - ' . $name;
    }

     public function user()
    {
        return $this->belongsTo(StoreUser::class, 'store_user_id');
    }
}
