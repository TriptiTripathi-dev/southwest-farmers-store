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
        // Validation and add logic
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        // ... implement adding logic here

        return redirect()->route('website.cart.index');
    }
}
