<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the products for the website.
     */
    public function index(Request $request)
    {
        // Simple example: paginate all products
        $products = Product::where('is_active', true)
            ->paginate(12);

        return view('website.products.index', compact('products'));
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        // Only show active products
        if (! $product->is_active) {
            abort(404);
        }

        return view('website.products.show', compact('product'));
    }
}
