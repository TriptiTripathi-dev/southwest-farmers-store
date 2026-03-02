<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StoreDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Display a listing of the products for the website.
     */
    public function index(Request $request)
    {
        $storeId = $this->getNearbyStoreId();

        $query = Product::where('is_active', true);
        
        if ($storeId) {
            $query->whereHas('storeStocks', function ($q) use ($storeId) {
                $q->where('store_id', $storeId)->where('quantity', '>', 0);
            });
        }

        $products = $query->paginate(12);

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
        $storeId = $this->getNearbyStoreId();

        if ($request->ajax()) {
            $query = Product::where('is_active', true);

            if ($storeId) {
                $query->whereHas('storeStocks', function ($q) use ($storeId) {
                    $q->where('store_id', $storeId)->where('quantity', '>', 0);
                });
            }

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

    /**
     * Get the nearby store ID based on user coordinates.
     */
    private function getNearbyStoreId()
    {
        $lat = null;
        $lng = null;

        if (Auth::guard('customer')->check()) {
            $customer = Auth::guard('customer')->user();
            $lat = $customer->latitude;
            $lng = $customer->longitude;
        } else {
            $lat = session('user_latitude');
            $lng = session('user_longitude');
        }

        if ($lat && $lng) {
            $store = StoreDetail::withinDistance($lat, $lng, 5)->first();
            return $store ? $store->id : null;
        }

        return null; // No location set or no store nearby
    }
}
