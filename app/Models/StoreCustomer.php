<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreCustomer extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'store_customers';

    protected $fillable = [
        'store_id',
        'name',
        'phone',
        'party_type',
        'email',
        'area',    
        'address',
        'due',
        'image',
        'is_active',
    ];

    // Helper to get image URL
    public function getImageUrlAttribute()
    {
        return $this->image 
            ? asset('storage/' . $this->image) 
            : 'https://placehold.co/100x100?text=No+Image';
    }
}