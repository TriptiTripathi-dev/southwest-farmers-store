<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'store_id', // <--- Added this
        'title',
        'message',
        'type',
        'url',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(StoreUser::class, 'user_id');
    }

    // New Relationship
    public function store()
    {
        return $this->belongsTo(StoreDetail::class, 'store_id');
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }
    
    public function markAsRead()
    {
        if (is_null($this->read_at)) {
            $this->update(['read_at' => now()]);
        }
    }
}