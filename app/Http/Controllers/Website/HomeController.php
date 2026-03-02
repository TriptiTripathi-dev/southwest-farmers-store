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
        $featuredProducts = Product::where('is_active', true)->take(8)->get();
        
        return view('website.home', compact('featuredProducts'));
    }

    /**
     * Display the contact page.
     */
    public function contact()
    {
        return view('website.contact');
    }

    /**
     * Display the about us page.
     */
    public function about()
    {
        return view('website.about');
    }

    /**
     * Handle contact form submission.
     */
    public function submitContact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string',
            'message' => 'required|string',
        ]);

        // Logic to send email or store in DB would go here
        
        return back()->with('success', 'Thank you for contacting us! We will get back to you soon.');
    }
}