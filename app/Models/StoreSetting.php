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
    ];

    protected $casts = [
        'sold_out_resets_daily' => 'boolean',
    ];

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