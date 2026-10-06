<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** One daily-availability change: which item, store, from/to, who and when. */
class MenuItemAvailabilityLog extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['menu_item_id', 'store_id', 'from_status', 'to_status', 'user_id', 'user_name'];

    protected $casts = ['created_at' => 'datetime'];

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class);
    }
}
