<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product; // Adjust models as needed

class HomeController extends Controller
{
    public function index()
    {
        // Fetch featured products, categories, etc. for the homepage
        $featuredProducts = \App\Models\Product::where('is_active', true)->take(8)->get();
        $homeSettings = \App\Models\HomePageSetting::first();
        
        return view('website.home', compact('featuredProducts', 'homeSettings'));
    }

    /**
     * Display the contact page.
     */
    public function contact()
    {
        $settings = \App\Models\ContactPageSetting::first();
        return view('website.contact', compact('settings'));
    }

    /**
     * Display the about us page.
     */
    public function about()
    {
        $settings = \App\Models\AboutPageSetting::first();
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
    public function legalPage($slug)
    {
        $page = \App\Models\LegalPage::where('slug', $slug)->firstOrFail();
        return view('website.legal', compact('page'));
    }
}