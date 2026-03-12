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
        'store_user_id',
        'pos_terminal_id',
        'pos_store_id',
        'pos_agent_secret',
        'pos_hardware_url',
        'pos_terminal_status'
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
    /**
     * Requirement 3.2: Location names must follow this format: SWF - (Location)
     */
    public function getFormattedNameAttribute()
    {
        if (str_starts_with($this->store_name, 'SWF -')) {
            return $this->store_name;
        }
        return "SWF - " . $this->store_name;
    }

    /**
     * Scope a query to only include stores within a given distance of a coordinate.
     */
    public function scopeWithinDistance($query, $latitude, $longitude, $distance = 5)
    {
        // Use Haversine formula for distance calculation in KM
        $haversine = "(6371 * acos(cos(radians($latitude)) 
                     * cos(radians(latitude)) 
                     * cos(radians(longitude) 
                     - radians($longitude)) 
                     + sin(radians($latitude)) 
                     * sin(radians(latitude))))";

        return $query->select('*')
            ->selectRaw("$haversine AS distance")
            ->havingRaw("$haversine < ?", [$distance])
            ->orderBy('distance');
    }
}
