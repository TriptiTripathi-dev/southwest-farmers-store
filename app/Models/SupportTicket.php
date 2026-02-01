<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class SupportTicket extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'sla_due_at' => 'datetime',
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    // Relationships
    public function createdBy() { return $this->morphTo(); }
    public function assignedTo() { return $this->belongsTo(WareUser::class, 'assigned_to_id'); }
    public function store() { return $this->belongsTo(StoreDetail::class); }
    public function messages() { return $this->hasMany(SupportMessage::class); }
    public function attachments() { return $this->hasMany(SupportAttachment::class); }
    public function logs() { return $this->hasMany(SupportStatusLog::class); }
    public function reference() { return $this->morphTo(); }

    // Helpers
    public function isOverdue()
    {
        return $this->status !== 'resolved' && $this->status !== 'closed' && $this->sla_due_at < now();
    }

    public function isOpen()
    {
        return !in_array($this->status, ['resolved', 'closed']);
    }

    // Scopes
    public function scopeForStore($query, $storeId)
    {
        return $query->where('store_id', $storeId);
    }
}