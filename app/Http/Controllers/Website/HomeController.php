<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product; // Adjust models as needed

class HomeController extends Controller
{
    public function index()
    {
        $storeId = session('store_id');
        
        // Fetch featured products, categories, etc. for the homepage
        // If a store is selected, perhaps filter products by store? 
        // For now, focusing on the settings as requested.
        $featuredProducts = \App\Models\Product::where('is_active', true)->take(8)->get();
        
        $homeSettings = \App\Models\HomePageSetting::where('store_id', $storeId)->first();
        if (!$homeSettings) {
            $homeSettings = \App\Models\HomePageSetting::whereNull('store_id')->first();
        }
        
        return view('website.home', compact('featuredProducts', 'homeSettings'));
    }

    /**
     * Display the contact page.
     */
    public function contact()
    {
        $storeId = session('store_id');
        $settings = \App\Models\ContactPageSetting::where('store_id', $storeId)->first();
        if (!$settings) {
            $settings = \App\Models\ContactPageSetting::whereNull('store_id')->first();
        }
        return view('website.contact', compact('settings'));
    }

    /**
     * Display the about us page.
     */
    public function about()
    {
        $storeId = session('store_id');
        $settings = \App\Models\AboutPageSetting::where('store_id', $storeId)->first();
        if (!$settings) {
            $settings = \App\Models\AboutPageSetting::whereNull('store_id')->first();
        }
        return view('website.about', compact('settings'));
    }

    /**
     * Handle contact form submission.
     */
    public function submitContact(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string',
            'message' => 'required|string',
        ]);

        \App\Models\Enquiry::create($data);
        
        return back()->with('success', 'Thank you for contacting us! We will get back to you soon.');
    }
    public function subscribeNewsletter(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $email = $request->email;
        $storeId = session('store_id');

        $exists = \App\Models\NewsletterSubscriber::where('email', $email)->exists();
        if ($exists) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are already subscribed to our newsletter!'
                ], 422);
            }
            return back()->with('error', 'You are already subscribed to our newsletter!');
        }

        \App\Models\NewsletterSubscriber::create([
            'email' => $email,
            'store_id' => $storeId,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Thank you for subscribing to our newsletter!'
            ]);
        }

        return back()->with('success', 'Thank you for subscribing to our newsletter!');
    }

    public function legalPage($slug)
    {
        $slugs = [$slug];
        if ($slug === 'terms-and-conditions') {
            $slugs[] = 'terms-and-condition';
        } elseif ($slug === 'terms-and-condition') {
            $slugs[] = 'terms-and-conditions';
        }

        $page = \App\Models\LegalPage::whereIn('slug', $slugs)->firstOrFail();
        return view('website.legal', compact('page'));
    }
}