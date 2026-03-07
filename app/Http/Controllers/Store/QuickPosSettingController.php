<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\QuickPosSetting;
use Illuminate\Http\Request;

class QuickPosSettingController extends Controller
{
    public function edit()
    {
        $settings = QuickPosSetting::first() ?: QuickPosSetting::create([]);
        return view('settings.quick_pos_page', compact('settings'));
    }

    public function update(Request $request)
    {
        $settings = QuickPosSetting::first();
        
        $data = $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string',
        ]);

        $settings->update($data);

        return back()->with('success', 'Quick POS settings updated successfully.');
    }
}
