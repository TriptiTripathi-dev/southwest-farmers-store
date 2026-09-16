<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Mirrors warehouse-pos's own WareNotification model — same shared
 * `ware_notifications` table. Lets the Store app drop an in-app notification
 * straight into a Warehouse user's notification bell (used by the Enquiry
 * escalation flow: Store -> Warehouse -> Main Super Admin).
 */
class WareNotification extends Model
{
    protected $table = 'ware_notifications';

    protected $fillable = [
        'user_id', 'title', 'message', 'type', 'url', 'read_at'
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];
}
