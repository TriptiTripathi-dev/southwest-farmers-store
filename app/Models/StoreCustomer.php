<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class StoreCustomer extends Authenticatable
{
    use HasFactory, SoftDeletes, Notifiable;

    protected $table = 'store_customers';

    protected $fillable = [
        'store_id',
        'name',
        'phone',
        'party_type',
        'email',
        'password',
        'area',    
        'address',
        'due',
        'image',
        'is_active',
        'latitude',
        'longitude',
        'source',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Helper to get image URL
    public function getImageUrlAttribute()
    {
        return $this->image 
            ? Storage::url($this->image) 
            : 'https://placehold.co/100x100?text=No+Image';
    }

    /**
     * Scope a query to only include customers within a given distance of a coordinate.
     */
    public function scopeWithinDistance($query, $latitude, $longitude, $distance = 50)
    {
        $haversine = "(6371 * acos(cos(radians($latitude)) 
                     * cos(radians(latitude)) 
                     * cos(radians(longitude) 
                     - radians($longitude)) 
                     + sin(radians($latitude)) 
                     * sin(radians(latitude))))";

        return $query->select('*')
            ->selectRaw("$haversine AS distance")
            ->havingRaw("$haversine <= ?", [$distance])
            ->orderBy('distance');
    }
}