<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Mirrors warehouse-pos's own WareRole model — same shared `ware_roles`
 * table. Added so the Store app can look up Warehouse staff by role (used
 * by the Enquiry escalation flow: Store -> Warehouse -> Main Super Admin).
 */
class WareRole extends Model
{
    protected $table = 'ware_roles';
    protected $fillable = ['name', 'guard_name'];

    public function users()
    {
        return $this->morphedByMany(WareUser::class, 'model', 'ware_model_has_roles', 'role_id', 'model_id');
    }
}
