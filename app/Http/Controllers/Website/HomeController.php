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
}