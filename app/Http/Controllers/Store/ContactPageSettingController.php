<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\ContactPageSetting;
use Illuminate\Http\Request;

class ContactPageSettingController extends Controller
{
    public function edit()
    {
        $settings = ContactPageSetting::first() ?: ContactPageSetting::create([]);
        return view('settings.contact_page', compact('settings'));
    }

    public function update(Request $request)
    {
        $settings = ContactPageSetting::first();
        
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
