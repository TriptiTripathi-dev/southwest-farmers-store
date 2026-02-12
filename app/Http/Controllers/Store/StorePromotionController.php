<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Promotion;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\StoreNotification;
use Illuminate\Support\Facades\Auth;

class StorePromotionController extends Controller
{
    public function index()
    {
        $promotions = Promotion::where('store_id', Auth::user()->store_id)
            ->with(['product', 'category'])
            ->latest()
            ->paginate(10);

        return view('store.promotions.index', compact('promotions'));
    }

    public function create()
    {
        // Get active products and categories for the dropdowns
        // Assuming products are filtered by store via StoreStock, 
        // but for defining a promo we generally look at the master list active in store.
        // Simplified query:
        $products = Product::where('is_active', true)->select('id', 'product_name')->get();
        $categories = ProductCategory::where('is_active', true)->select('id', 'name')->get();

        return view('store.promotions.create', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:percentage,fixed_amount,bogo',
            'value' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'scope' => 'required|in:product,category,global'
        ]);

        $data = $request->except(['scope']);
        $data['store_id'] = Auth::user()->store_id;
        $data['is_active'] = true;

        // Handle Scope (Product vs Category)
        if ($request->scope == 'product') {
            $request->validate(['product_id' => 'required|exists:products,id']);
            $data['product_id'] = $request->product_id;
            $data['category_id'] = null;
        } elseif ($request->scope == 'category') {
            $request->validate(['category_id' => 'required|exists:product_categories,id']);
            $data['category_id'] = $request->category_id;
            $data['product_id'] = null;
        }

       $promo = Promotion::create($data);
        StoreNotification::create([
            'user_id' => Auth::id(),
            'store_id' => Auth::user()->store_id,
            'title' => 'Promotion Created',
            'message' => "Campaign '{$promo->name}' created successfully.",
            'type' => 'success',
            'url' => route('store.promotions.index'),
        ]);

        return redirect()->route('store.promotions.index')->with('success', 'Promotion created successfully!');
    }

    public function destroy($id)
    {
        $promo = Promotion::where('store_id', Auth::user()->store_id)->findOrFail($id);
        $promo->delete();
        return back()->with('success', 'Promotion deleted.');
    }
    
    public function updateStatus(Request $request, $id)
    {
        $promo = Promotion::where('store_id', Auth::user()->store_id)->findOrFail($id);
        $promo->update(['is_active' => !$promo->is_active]);
        return back()->with('success', 'Status updated.');
    }
}