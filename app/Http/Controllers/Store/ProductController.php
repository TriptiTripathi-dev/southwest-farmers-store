<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory; // Import Category Model
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display the product list with dynamic filtering.
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'subcategory']);

        // 1. Search Filter
      if ($request->filled('search')) {
    $search = $request->search;
    $query->where(function($q) use ($search) {
        $q->where('product_name', 'ilike', "%{$search}%")   // Change 'like' to 'ilike'
          ->orWhere('sku', 'ilike', "%{$search}%")          // Change 'like' to 'ilike'
          ->orWhere('barcode', 'ilike', "%{$search}%");     // Change 'like' to 'ilike'
    });
}

        // 2. Status Filter
        if ($request->filled('status')) {
            $isActive = $request->status === 'active' ? 1 : 0;
            $query->where('is_active', $isActive);
        }

        // 3. Category Filter (Added this)
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Fetch Data
        $products = $query->latest()
            ->paginate(10)
            ->withQueryString();

        // Fetch Categories for the Filter Dropdown
        $categories = ProductCategory::select('id', 'name')->get();

        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Fetch single product details for Modal (AJAX).
     */
    public function show($id)
    {
        $product = Product::with(['category', 'subcategory', 'option'])->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }
}