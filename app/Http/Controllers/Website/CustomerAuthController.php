<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class CustomerAuthController extends Controller
{
    // ─── Login ─────────────────────────────────────────────────────────

    public function showLogin()
    {
        if (Auth::guard('customer')->check()) {
            return redirect()->route('website.dashboard');
        }
        return view('website.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('customer')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('website.dashboard'));
        }

        return back()->withErrors([
            'email' => 'These credentials do not match our records.',
        ])->onlyInput('email');
    }

    // ─── Register ───────────────────────────────────────────────────────

    public function showRegister()
    {
        if (Auth::guard('customer')->check()) {
            return redirect()->route('website.dashboard');
        }
        return view('website.auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'email', 'max:255', 'unique:customers,email'],
            'phone'         => ['nullable', 'string', 'max:20'],
            'gender'        => ['nullable', 'in:male,female,other'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'address'       => ['nullable', 'string', 'max:500'],
            'city'          => ['nullable', 'string', 'max:100'],
            'state'         => ['nullable', 'string', 'max:100'],
            'zip_code'      => ['nullable', 'string', 'max:20'],
            'country'       => ['nullable', 'string', 'max:100'],
            'password'      => ['required', 'confirmed', Password::min(8)],
        ]);

        $customer = Customer::create([
            'name'          => $data['name'],
            'email'         => $data['email'],
            'phone'         => $data['phone'] ?? null,
            'gender'        => $data['gender'] ?? null,
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'address'       => $data['address'] ?? null,
            'city'          => $data['city'] ?? null,
            'state'         => $data['state'] ?? null,
            'zip_code'      => $data['zip_code'] ?? null,
            'country'       => $data['country'] ?? 'India',
            'password'      => Hash::make($data['password']),
        ]);

        Auth::guard('customer')->login($customer);

        return redirect()->route('website.dashboard')
            ->with('success', 'Welcome, ' . $customer->name . '! Your account has been created.');
    }

    // ─── Dashboard / Account ─────────────────────────────────────────────

    public function dashboard()
    {
        $customer = Auth::guard('customer')->user();
        return view('website.customer.dashboard', compact('customer'));
    }

    // ─── Logout ─────────────────────────────────────────────────────────

    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('website.home')->with('success', 'You have been logged out.');
    }
}
