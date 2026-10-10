<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** "Orders with min..max days' notice -> surcharge X% / block" for one store (kitchen spec 8.1). */
class CateringUrgencyTier extends Model
{
    protected $fillable = ['store_id', 'min_days', 'max_days', 'action', 'percent'];

    protected $casts = ['min_days' => 'integer', 'max_days' => 'integer', 'percent' => 'float'];

    public function label(): string
    {
        $days = $this->min_days === $this->max_days ? "{$this->min_days} day" . ($this->min_days === 1 ? '' : 's') : "{$this->min_days}–{$this->max_days} days";

        return $this->action === 'block'
            ? "{$days}' notice: not accepted"
            : "{$days}' notice: +" . rtrim(rtrim(number_format($this->percent, 2), '0'), '.') . '% urgency surcharge';
    }
}
