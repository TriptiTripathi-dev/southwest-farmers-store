<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;

class CartController extends Controller
{
    /**
     * Show the user's cart.
     */
    public function index(Request $request)
    {
        $cart = Cart::with('items.product')
            ->where('user_id', auth()->id())
            ->first();

        return view('website.cart.index', compact('cart'));
    }

    /**
     * Add an item to the cart.
     */
    public function store(Request $request)
    {

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);
        $user = auth()->user();
        $storeId = $user->store_id ?? 1; // Default or handled by logic

        // Get or Create Active Cart
        $cart = Cart::firstOrCreate(
            [
                'user_id' => $user->id,
                'status' => 'active'
            ],
            [
                'store_id' => $storeId,
                'total_amount' => 0
            ]
        );

        $product = \App\Models\Product::find($request->product_id);
        
        // Check if item exists in cart
        $cartItem = $cart->items()->where('product_id', $product->id)->first();

        if ($cartItem) {
            $cartItem->quantity += $request->quantity;
            $cartItem->total = $cartItem->quantity * $product->price;
            $cartItem->save();
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'price' => $product->price,
                'total' => $product->price * $request->quantity
            ]);
        }

        // Recalculate Total
        $cart->update(['total_amount' => $cart->items()->sum('total')]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Product added to cart!',
                'cart_count' => $cart->items()->count()
            ]);
        }

        return redirect()->route('website.cart.index')->with('success', 'Product added to cart!');
    }
}
