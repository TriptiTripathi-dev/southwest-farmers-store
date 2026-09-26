<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class StoreUser extends Authenticatable
{
    use Notifiable, SoftDeletes, HasRoles, HasFactory;

    protected $table = 'store_users';
    protected $guard_name = 'store_user';

    protected $fillable = [
        'store_id',        // Store ID
        'parent_id',     // Kisne create kiya (Store Owner)
        'name',
        'email',
        'password',
        'phone',
        'profile',
        'store_role_id',   // Yahan Role ID store karenge
        'is_active',
        'is_website_manager', // New field for Website Manager role
        'staff_code',      // Unique per-employee code used to clock in/out
        'profile_photo',   // Personal photo (R2 path); 'profile' holds the store logo
        'store_group_id',  // Multi-location access, e.g. a Regional Manager (client PDF 9/22, item 6)
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
        'is_website_manager' => 'boolean',
    ];


    public function store()
    {
        return $this->belongsTo(StoreDetail::class, 'store_id');
    }

    /**
     * Item 1: lets a Super Admin "view as" a different location without a
     * separate login. Only ever overridden for the currently authenticated
     * user's own model instance -- other StoreUser rows loaded elsewhere
     * (e.g. a staff list) must keep reflecting their real store_id.
     * store.switch-location is itself gated to Super Admins, so by the time
     * this session key exists it was set legitimately.
     */
    public function getStoreIdAttribute($value)
    {
        if (session()->has('active_store_id')
            && \Illuminate\Support\Facades\Auth::guard('store')->check()
            && \Illuminate\Support\Facades\Auth::guard('store')->id() === $this->getKey()) {
            return session('active_store_id');
        }

        return $value;
    }

    public function storeGroup()
    {
        return $this->belongsTo(StoreGroup::class, 'store_group_id');
    }

    /**
     * Locations this user may switch between from the header: every active
     * store for a Super Admin, the active stores of their group for anyone
     * assigned a Store Group, otherwise none (single-location staff).
     */
    public function switchableStores()
    {
        if ($this->hasRole('Super Admin')) {
            return StoreDetail::where('is_active', true)->orderBy('store_name')->get();
        }

        if ($this->store_group_id) {
            return StoreDetail::where('is_active', true)
                ->where('store_group_id', $this->store_group_id)
                ->orderBy('store_name')
                ->get();
        }

        return collect();
    }

    public function canSwitchToStore(int $storeId): bool
    {
        return $this->switchableStores()->contains('id', $storeId);
    }

    public function timeLogs()
    {
        return $this->hasMany(StoreTimeLog::class, 'store_user_id');
    }


    // Relationship: Parent (Owner)
    public function parent()
    {
        return $this->belongsTo(StoreUser::class, 'parent_id');
    }

    // Custom Roles Relationship (Already set up previously)
    public function roles()
    {
        return $this->belongsToMany(
            \App\Models\StoreRole::class,
            'store_model_has_roles',
            'model_id',
            'role_id'
        )->wherePivot('model_type', self::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(
            \App\Models\StorePermission::class,
            'store_model_has_permissions',
            'model_id',
            'permission_id'
        )->wherePivot('model_type', self::class);
    }


    public function hasPermission($permissionName)
    {
        // Super Admin bypass
        if ($this->isAdmin()) {
            return true;
        }

        // 1. Direct permissions
        if ($this->permissions->contains('name', $permissionName)) {
            return true;
        }

        // 2. Via roles
        foreach ($this->roles as $role) {
            if ($role->permissions->contains('name', $permissionName)) {
                return true;
            }
        }

        return false;
    }

    public function isAdmin()
    {
        return $this->email === 'admin@store.com';
    }

    /** Public URL of the uploaded profile photo, or null to fall back to initials. */
    public function getProfilePhotoUrlAttribute(): ?string
    {
        return $this->profile_photo ? \Illuminate\Support\Facades\Storage::disk('r2')->url($this->profile_photo) : null;
    }

    /** "Eric Odom" -> "EO", "Cashier" -> "C". */
    public function getInitialsAttribute(): string
    {
        $words = preg_split('/\s+/', trim((string) $this->name), -1, PREG_SPLIT_NO_EMPTY);
        if (! $words) {
            return '?';
        }
        $first = mb_substr($words[0], 0, 1);
        $last = count($words) > 1 ? mb_substr(end($words), 0, 1) : '';

        return mb_strtoupper($first . $last);
    }
}
