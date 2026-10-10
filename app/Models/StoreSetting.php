<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreSetting extends Model
{
    protected $table = 'store_settings';

    protected $fillable = [
        'store_id',
        'app_name',
        'app_phone',
        'support_email',
        'address',
        'logo',
        'favicon',
        'login_logo',
        'currency',       // Added
        'vat_percentage', // Added
        'footer_description',
        'facebook_url',
        'instagram_url',
        'twitter_url',
        'sold_out_display',      // show | hide: Sold Out items on the website
        'sold_out_resets_daily', // Sold Out clears by itself the next day
        'catering_enabled',
        'catering_notice_days',  // standard notice, default 7 calendar days
        'catering_cutoff_time',  // HH:MM local; orders after it count from the next day
    ];

    protected $casts = [
        'sold_out_resets_daily' => 'boolean',
        'catering_enabled' => 'boolean',
    ];

    /** Catering options for a store, with the contract defaults when unset. */
    public static function cateringOptions(?int $storeId): array
    {
        $row = $storeId ? static::where('store_id', $storeId)->first() : null;
        $cutoff = (string) ($row?->catering_cutoff_time ?: '17:00');

        return [
            'enabled' => $row?->catering_enabled ?? true,
            'notice_days' => max(0, (int) ($row?->catering_notice_days ?? 7)),
            'cutoff_time' => preg_match('/^\d{2}:\d{2}$/', $cutoff) ? $cutoff : '17:00',
        ];
    }

    /** Kitchen availability options for a store, with the defaults when unset. */
    public static function availabilityOptions(?int $storeId): array
    {
        $row = $storeId ? static::where('store_id', $storeId)->first() : null;

        return [
            'sold_out_display' => in_array($row?->sold_out_display, ['show', 'hide'], true) ? $row->sold_out_display : 'show',
            'sold_out_resets_daily' => $row?->sold_out_resets_daily ?? true,
        ];
    }
}