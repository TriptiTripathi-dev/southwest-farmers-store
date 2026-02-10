<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\StoreSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class GeneralSettingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $settings = StoreSetting::first();
        return view('settings.general', compact('settings'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $settings = StoreSetting::where('store_id', $user->store_id)->firstOrFail();

        $request->validate([
            'app_name' => 'required|string|max:255',
            'app_phone' => 'nullable|string|max:20',
            'support_email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'logo' => 'nullable|image|max:2048',
            'favicon' => 'nullable|image|max:1024',
            'login_logo' => 'nullable|image|max:2048',
            'currency' => 'nullable|string|max:10',
            'vat_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        $data = $request->only([
            'app_name', 
            'app_phone', 
            'support_email', 
            'address', 
            'currency', 
            'vat_percentage'
        ]);

        $uploadFile = function ($fileKey, $path) use ($request, $settings) {
            if ($request->hasFile($fileKey)) {
                if ($settings->{$fileKey}) {
                    Storage::disk('public')->delete($settings->{$fileKey});
                }
                return $request->file($fileKey)->store($path, 'public');
            }
            return $settings->{$fileKey};
        };

        $data['logo'] = $uploadFile('logo', 'store/branding');
        $data['favicon'] = $uploadFile('favicon', 'store/branding');
        $data['login_logo'] = $uploadFile('login_logo', 'store/branding');

        $settings->update($data);

        return back()->with('success', 'Settings updated successfully.');
    }
}