<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

class StoreUser extends Authenticatable
{
    use Notifiable, SoftDeletes, HasRoles;

    protected $table = 'store_users';
    protected $guard_name = 'store_user';

    protected $fillable = [
        'parent_id',     // Kisne create kiya (Store Owner)
        'name',
        'email',
        'password',
        'phone',
        'profile',
        'store_role_id',   // Yahan Role ID store karenge
        'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

   
    public function store()
    {
        return $this->belongsTo(StoreDetail::class, 'store_id');
    }
    

    // Relationship: Parent (Owner)
    public function parent()
    {
        return $this->belongsTo(StoreUser::class, 'parent_id');
    }

    // Custom Roles Relationship (Already set up previously)
    public function roles()
    {
        return $this->morphToMany(
            StoreRole::class,
            'model',
            'store_model_has_roles',
            'model_id',
            'role_id'
        );
    }
}