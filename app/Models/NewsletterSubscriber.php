<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsletterSubscriber extends Model
{
    protected $fillable = [
        'email',
        'store_id',
    ];

    public function store()
    {
        return $this->belongsTo(StoreDetail::class, 'store_id');
    }
}
