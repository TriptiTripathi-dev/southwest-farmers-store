<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class StorePermission extends SpatiePermission
{
    protected $table = 'store_permissions';

    protected $fillable = ['name', 'guard_name', 'group_name'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setTable('store_permissions');
    }

    /**
     * Override the default roles relationship to use custom tables
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            StoreRole::class,
            'store_role_has_permissions', // Custom pivot table
            'permission_id',
            'role_id'
        );
    }
}