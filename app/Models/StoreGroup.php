<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A named set of stores, managed on the Warehouse side (Stores -> Store
 * Groups). Staff with a store_group_id -- typically a Regional Manager --
 * can switch between every active store in their group.
 */
class StoreGroup extends Model
{
    protected $fillable = ['name', 'description', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function stores()
    {
        return $this->hasMany(StoreDetail::class, 'store_group_id');
    }
}
