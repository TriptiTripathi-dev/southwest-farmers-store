<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany; // Import This

class StoreRole extends SpatieRole
{
    protected $table = 'store_roles';
    public $guard_name = 'store_user';

    protected $fillable = ['name', 'guard_name'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setTable('store_roles');
    }

    /**
     * Override Permissions Relationship
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            StorePermission::class,
            'store_role_has_permissions',
            'role_id',
            'permission_id'
        );
    }

    /**
     * ADD THIS: Override Users Relationship
     * Jab role delete hoga, to ye relationship bataega ki kaunse users se detach karna hai.
     */
    public function users(): MorphToMany
    {
        return $this->morphedByMany(
            StoreUser::class,             // Aapka Custom User Model
            'model',                      // Relationship Name (Spatie default 'model' use karta hai)
            'store_model_has_roles',      // Custom Pivot Table
            'role_id',                    // Foreign Key for Role
            'model_id'                    // Foreign Key for User
        );
    }
}