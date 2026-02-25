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

    /**
     * Display a POS-style product page for the website.
     */
    public function pos(Request $request)
    {
        $categories = \App\Models\ProductCategory::select('id', 'name', 'code')->orderBy('name')->get();

        if ($request->ajax()) {
            $query = Product::where('is_active', true);

            // Filter by Category
            if ($request->category && $request->category !== 'all') {
                $query->whereHas('category', function ($q) use ($request) {
                    $q->where('code', $request->category);
                });
            }

            // Search Term
            if ($request->term) {
                $query->where(function ($q) use ($request) {
                    $term = $request->term;
                    $q->where('product_name', 'LIKE', "%{$term}%")
                      ->orWhere('sku', 'LIKE', "%{$term}%");
                });
            }

            $products = $query->limit(24)->get();

            return response()->json($products);
        }

        return view('website.products.pos', compact('categories'));
    }
}
