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
        
        // Fetch or create empty settings for this store
        $settings = StoreSetting::first();

        return view( 'settings.general', compact('settings'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $settings = StoreSetting::where('store_id', $user->id)->firstOrFail();

        $request->validate([
            'app_name' => 'required|string|max:255',
            'app_phone' => 'nullable|string|max:20',
            'support_email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'logo' => 'nullable|image|max:2048', // 2MB Max
            'favicon' => 'nullable|image|max:1024',
            'login_logo' => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['app_name', 'app_phone', 'support_email', 'address']);

        // Helper function for file upload
        $uploadFile = function ($fileKey, $path) use ($request, $settings) {
            if ($request->hasFile($fileKey)) {
                // Delete old file if exists
                if ($settings->{$fileKey}) {
                    Storage::disk('public')->delete($settings->{$fileKey});
                }
                return $request->file($fileKey)->store($path, 'public');
            }
            return $settings->{$fileKey}; // Keep existing
        };

        $data['logo'] = $uploadFile('logo', 'store/branding');
        $data['favicon'] = $uploadFile('favicon', 'store/branding');
        $data['login_logo'] = $uploadFile('login_logo', 'store/branding');

        $settings->update($data);

        return back()->with('success', 'Settings updated successfully.');
    }
}