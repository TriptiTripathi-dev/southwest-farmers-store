<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

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
}