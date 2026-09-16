<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enquiry extends Model
{
    protected $fillable = [
        'name',
        'email',
        'subject',
        'message',
        'is_read',
        'status',
        'escalated_at',
        'escalated_to_admin_at',
        'resolved_at',
        'resolution_notes',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'escalated_at' => 'datetime',
        'escalated_to_admin_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    // Escalation stages: new -> escalated_warehouse -> escalated_admin -> resolved
    const STATUS_NEW = 'new';
    const STATUS_ESCALATED_WAREHOUSE = 'escalated_warehouse';
    const STATUS_ESCALATED_ADMIN = 'escalated_admin';
    const STATUS_RESOLVED = 'resolved';

    public function isNew()
    {
        return ($this->status ?? self::STATUS_NEW) === self::STATUS_NEW;
    }

    public function isEscalated()
    {
        return in_array($this->status, [self::STATUS_ESCALATED_WAREHOUSE, self::STATUS_ESCALATED_ADMIN]);
    }

    public function isResolved()
    {
        return $this->status === self::STATUS_RESOLVED;
    }
}
