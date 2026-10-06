<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'store_id',
        'menu_category_id',
        'name',
        'description',
        'price',
        'image',
        'is_active',
        'is_pre_cooked',
        'daily_target_quantity',
        'is_catering_only',
        'advance_notice_days',
        'rush_fee_percentage',
        'available_days',
        'is_available_today',
        'availability_status',
        'sold_out_on',
        'availability_changed_at',
        'availability_changed_by',
    ];

    protected $casts = [
        'price' => 'float',
        'is_active' => 'boolean',
        'is_pre_cooked' => 'boolean',
        'is_catering_only' => 'boolean',
        'is_available_today' => 'boolean',
        'rush_fee_percentage' => 'float',
        'available_days' => 'array',
        'sold_out_on' => 'date',
        'availability_changed_at' => 'datetime',
    ];

    // Daily availability (kitchen spec 4.3): set by kitchen staff per store.
    public const AVAILABLE = 'available';
    public const UNAVAILABLE = 'unavailable';
    public const SOLD_OUT = 'sold_out';
    public const NOT_SCHEDULED = 'not_scheduled'; // computed: not on today's weekly schedule

    public const STATUS_LABELS = [
        self::AVAILABLE => 'Available',
        self::UNAVAILABLE => 'Unavailable',
        self::SOLD_OUT => 'Sold Out',
        self::NOT_SCHEDULED => 'Not on today\'s schedule',
    ];

    /** Today in the store's local time (Central). */
    public static function localToday(): \Carbon\Carbon
    {
        return now(config('app.display_timezone', 'America/Chicago'))->startOfDay();
    }

    /**
     * What customers can do with this item right now. Sold Out from an
     * earlier day counts as Available again when the store resets daily.
     * Unavailable stays until someone changes it.
     */
    public function currentStatus(bool $soldOutResetsDaily = true): string
    {
        $status = $this->availability_status ?: self::AVAILABLE;

        if ($status === self::SOLD_OUT && $soldOutResetsDaily
            && (!$this->sold_out_on || $this->sold_out_on->toDateString() < self::localToday()->toDateString())) {
            $status = self::AVAILABLE;
        }

        if ($status === self::AVAILABLE && is_array($this->available_days) && $this->available_days !== []
            && !in_array(self::localToday()->format('D'), $this->available_days, true)) {
            return self::NOT_SCHEDULED;
        }

        return $status;
    }

    public function isOrderableNow(bool $soldOutResetsDaily = true): bool
    {
        return $this->is_active && $this->currentStatus($soldOutResetsDaily) === self::AVAILABLE;
    }

    public function availabilityLogs()
    {
        return $this->hasMany(MenuItemAvailabilityLog::class);
    }

    public function store()
    {
        return $this->belongsTo(StoreDetail::class, 'store_id');
    }

    public function category()
    {
        return $this->belongsTo(MenuCategory::class, 'menu_category_id');
    }
}
