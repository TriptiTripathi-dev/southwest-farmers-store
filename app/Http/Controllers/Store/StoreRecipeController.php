<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductIngredient;
use Illuminate\Support\Facades\Auth;

class StoreRecipeController extends Controller
{
    // Show Recipe Management Page for a Product
    public function edit($productId)
    {
        $storeId = Auth::user()->store_id;
        
        $product = Product::with('ingredients.ingredient')->findOrFail($productId);
        
        // Get potential ingredients (all active products except itself)
        // Ideally, you might want to filter this by a "Raw Material" category if you have one.
        $allProducts = Product::where('is_active', true)
            ->where('id', '!=', $productId)
            ->orderBy('product_name')
            ->get();

        return view('store.products.recipe', compact('product', 'allProducts'));
    }

    // Add an Ingredient
    public function store(Request $request, $productId)
    {
        $request->validate([
            'ingredient_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0.001'
        ]);

        ProductIngredient::updateOrCreate(
            [
                'product_id' => $productId,
                'ingredient_id' => $request->ingredient_id
            ],
            [
                'quantity' => $request->quantity
            ]
        );

        return back()->with('success', 'Ingredient added/updated.');
    }

    // Remove an Ingredient
    public function destroy($id)
    {
        ProductIngredient::destroy($id);
        return back()->with('success', 'Ingredient removed.');
    }
}