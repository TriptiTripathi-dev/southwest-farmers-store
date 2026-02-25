<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\StoreCustomer;
use App\Models\StoreDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class CustomerAuthController extends Controller
{
    // ─── Login ─────────────────────────────────────────────────────────

    public function showLoginForm()
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
            // Try to redirect to intended or dashboard
            return redirect()->intended(route('website.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    // ─── Register ───────────────────────────────────────────────────────

    public function showRegistrationForm()
    {
        if (Auth::guard('customer')->check()) {
            return redirect()->route('website.dashboard');
        }
        return view('website.auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'email', 'max:255', 'unique:store_customers,email'],
            'phone'         => ['required', 'string', 'max:20'],
            'password'      => ['required', 'confirmed', Password::min(8)],
            'address'       => ['nullable', 'string'],
            'area'          => ['nullable', 'string', 'max:255'],
        ]);

        // Default to the first active store
        $store = StoreDetail::where('is_active', true)->first();
        $store_id = $store ? $store->id : 1;

        $customer = StoreCustomer::create([
            'store_id'   => $store_id,
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'phone'      => $request->phone,
            'address'    => $request->address,
            'area'       => $request->area,
            'party_type' => 'Retail',
            'is_active'  => true,
        ]);

        Auth::guard('customer')->login($customer);

        return redirect()->route('website.dashboard')
            ->with('success', 'Account created successfully! Welcome to FreshStore.');
    }

    // ─── Dashboard ──────────────────────────────────────────────────────

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
