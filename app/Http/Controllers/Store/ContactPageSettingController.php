<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\ContactPageSetting;
use Illuminate\Http\Request;

class ContactPageSettingController extends Controller
{
    public function edit()
    {
        $storeId = auth()->user()->store_id ?? null;
        $settings = ContactPageSetting::where('store_id', $storeId)->first();
        if (!$settings) {
            $settings = ContactPageSetting::create(['store_id' => $storeId]);
        }
        return view('settings.contact_page', compact('settings'));
    }

    public function update(Request $request)
    {
        $storeId = auth()->user()->store_id ?? null;
        $settings = ContactPageSetting::where('store_id', $storeId)->first();
        if (!$settings) {
            $settings = ContactPageSetting::create(['store_id' => $storeId]);
        }
        
        $data = $request->validate([
            'header_badge' => 'nullable|string|max:255',
            'header_title' => 'nullable|string|max:255',
            'header_subtitle' => 'nullable|string',
            'address_title' => 'nullable|string|max:255',
            'address_content' => 'nullable|string',
            'phone_title' => 'nullable|string|max:255',
            'phone_content' => 'nullable|string',
            'email_title' => 'nullable|string|max:255',
            'email_content' => 'nullable|string',
            'form_title' => 'nullable|string|max:255',
        ]);

        $settings->update($data);

        return back()->with('success', 'Contact page settings updated successfully.');
    }
}
