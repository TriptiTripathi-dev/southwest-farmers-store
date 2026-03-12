<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomePageSetting extends Model
{
    protected $fillable = [
        'store_id',
        'hero_badge',
        'hero_title',
        'hero_subtitle',
        'hero_button_text',
        'hero_button_url',
        'hero_image',
        'features_title',
        'features_subtitle',
        'feature_1_title',
        'feature_1_text',
        'feature_1_icon',
        'feature_2_title',
        'feature_2_text',
        'feature_2_icon',
        'feature_3_title',
        'feature_3_text',
        'feature_3_icon',
        'trending_title',
        'trending_subtitle',
        'cta_title',
        'cta_subtitle',
        'cta_button_1_text',
        'cta_button_1_url',
        'cta_button_2_text',
        'cta_button_2_url',
    ];

    public function store()
    {
        return $this->belongsTo(StoreDetail::class, 'store_id');
    }
}
