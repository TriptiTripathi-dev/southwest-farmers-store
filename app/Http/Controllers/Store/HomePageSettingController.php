<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\HomePageSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomePageSettingController extends Controller
{
    public function edit()
    {
        $storeId = auth()->user()->store_id ?? null;
        $settings = HomePageSetting::where('store_id', $storeId)->first();
        if (!$settings) {
            $settings = HomePageSetting::create(['store_id' => $storeId]);
        }
        return view('settings.home_page', compact('settings'));
    }

    public function update(Request $request)
    {
        $storeId = auth()->user()->store_id ?? null;
        $settings = HomePageSetting::where('store_id', $storeId)->first();
        if (!$settings) {
            $settings = HomePageSetting::create(['store_id' => $storeId]);
        }

        $request->validate([
            'hero_badge' => 'nullable|string|max:255',
            'hero_title' => 'nullable|string|max:255',
            'hero_subtitle' => 'nullable|string',
            'hero_button_text' => 'nullable|string|max:255',
            'hero_button_url' => 'nullable|string|max:255',
            'hero_image' => 'nullable|image|max:2048',
            'features_title' => 'nullable|string|max:255',
            'features_subtitle' => 'nullable|string',
            'feature_1_title' => 'nullable|string|max:255',
            'feature_1_text' => 'nullable|string',
            'feature_1_icon' => 'nullable|string|max:255',
            'feature_2_title' => 'nullable|string|max:255',
            'feature_2_text' => 'nullable|string',
            'feature_2_icon' => 'nullable|string|max:255',
            'feature_3_title' => 'nullable|string|max:255',
            'feature_3_text' => 'nullable|string',
            'feature_3_icon' => 'nullable|string|max:255',
            'trending_title' => 'nullable|string|max:255',
            'trending_subtitle' => 'nullable|string',
            'cta_title' => 'nullable|string|max:255',
            'cta_subtitle' => 'nullable|string',
            'cta_button_1_text' => 'nullable|string|max:255',
            'cta_button_1_url' => 'nullable|string|max:255',
            'cta_button_2_text' => 'nullable|string|max:255',
            'cta_button_2_url' => 'nullable|string|max:255',
        ]);

        $data = $request->except(['hero_image', '_token', '_method']);

        if ($request->hasFile('hero_image')) {
            if ($settings->hero_image) {
                Storage::disk('r2')->delete($settings->hero_image);
            }
            $data['hero_image'] = $request->file('hero_image')->store('website/home', 'r2');
        }

        $settings->update($data);

        return back()->with('success', 'Home page settings updated successfully.');
    }
}
