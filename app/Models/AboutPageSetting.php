<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutPageSetting extends Model
{
    protected $fillable = [
        'hero_title',
        'hero_subtitle',
        'mission_badge',
        'mission_title',
        'mission_text',
        'mission_image',
        'stat_1_value',
        'stat_1_label',
        'stat_2_value',
        'stat_2_label',
        'stat_3_value',
        'stat_3_label',
        'stat_4_value',
        'stat_4_label',
        'values_title',
        'value_1_title',
        'value_1_text',
        'value_1_icon',
        'value_2_title',
        'value_2_text',
        'value_2_icon',
        'value_3_title',
        'value_3_text',
        'value_3_icon',
        'cta_title',
        'cta_subtitle',
    ];
}
