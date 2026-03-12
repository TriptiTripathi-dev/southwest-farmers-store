<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\AboutPageSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AboutPageSettingController extends Controller
{
    public function edit()
    {
        $storeId = auth()->user()->store_id ?? null;
        $settings = AboutPageSetting::where('store_id', $storeId)->first();
        if (!$settings) {
            $settings = AboutPageSetting::create(['store_id' => $storeId]);
        }
        return view('settings.about_page', compact('settings'));
    }

    public function update(Request $request)
    {
        $storeId = auth()->user()->store_id ?? null;
        $settings = AboutPageSetting::where('store_id', $storeId)->first();
        if (!$settings) {
            $settings = AboutPageSetting::create(['store_id' => $storeId]);
        }
        
        $data = $request->validate([
            'hero_title' => 'nullable|string|max:255',
            'hero_subtitle' => 'nullable|string',
            'mission_badge' => 'nullable|string|max:255',
            'mission_title' => 'nullable|string|max:255',
            'mission_text' => 'nullable|string',
            'mission_image' => 'nullable|image|max:2048',
            'stat_1_value' => 'nullable|string|max:255',
            'stat_1_label' => 'nullable|string|max:255',
            'stat_2_value' => 'nullable|string|max:255',
            'stat_2_label' => 'nullable|string|max:255',
            'stat_3_value' => 'nullable|string|max:255',
            'stat_3_label' => 'nullable|string|max:255',
            'stat_4_value' => 'nullable|string|max:255',
            'stat_4_label' => 'nullable|string|max:255',
            'values_title' => 'nullable|string|max:255',
            'value_1_title' => 'nullable|string|max:255',
            'value_1_text' => 'nullable|string',
            'value_1_icon' => 'nullable|string|max:255',
            'value_2_title' => 'nullable|string|max:255',
            'value_2_text' => 'nullable|string',
            'value_2_icon' => 'nullable|string|max:255',
            'value_3_title' => 'nullable|string|max:255',
            'value_3_text' => 'nullable|string',
            'value_3_icon' => 'nullable|string|max:255',
            'cta_title' => 'nullable|string|max:255',
            'cta_subtitle' => 'nullable|string',
        ]);

        if ($request->hasFile('mission_image')) {
            if ($settings->mission_image) {
                Storage::disk('public')->delete($settings->mission_image);
            }
            $data['mission_image'] = $request->file('mission_image')->store('website/about', 'public');
        }

        $settings->update($data);

        return back()->with('success', 'About page settings updated successfully.');
    }
}
