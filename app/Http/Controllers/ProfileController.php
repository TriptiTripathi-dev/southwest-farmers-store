<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{

    public function edit()
    {
        return view('profile.edit', [
            'user' => Auth::user(),
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        if (! $user) {
            return back()->withErrors([
                'general' => 'User not authenticated.',
            ]);
        }

        // Outside the try below: it used to swallow validation errors into a
        // generic "Something went wrong". Email must stay unique, same rule
        // as the Staff screen (client PDF 9/21, Store item 1).
        $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('store_users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        try {
            $user->update([
                'name'  => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
            ]);

            return back()->with('success', 'Profile updated successfully.');
        } catch (\Exception $e) {
            return back()->withErrors([
                'general' => 'Something went wrong while updating profile.',
            ]);
        }
    }

    /** Upload or replace the signed-in user's profile photo (client PDF 9/22, Store item 1). */
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
        ]);

        $user = Auth::user();
        $old = $user->profile_photo;

        $user->update(['profile_photo' => $request->file('photo')->store('staff-photos', 'r2')]);

        if ($old && Storage::disk('r2')->exists($old)) {
            Storage::disk('r2')->delete($old);
        }

        return back()->with('success', 'Profile photo updated.');
    }

    /** Remove the photo; the avatar falls back to the user's initials. */
    public function removePhoto()
    {
        $user = Auth::user();

        if ($user->profile_photo && Storage::disk('r2')->exists($user->profile_photo)) {
            Storage::disk('r2')->delete($user->profile_photo);
        }
        $user->update(['profile_photo' => null]);

        return back()->with('success', 'Profile photo removed.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password' => [
                'required',
                'confirmed', 
                Password::defaults(),
            ],
        ]);

        $user = Auth::user();

        if (! $user) {
            return back()->withErrors([
                'general' => 'User not authenticated.',
            ]);
        }

        if (! Hash::check($request->current_password, $user->password)) {
            return back()
                ->withErrors([
                    'error' => 'Old password does not match.',
                ])
                ->withInput();
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password updated successfully.');
    }
}
