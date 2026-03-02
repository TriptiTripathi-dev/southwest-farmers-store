<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;

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

        if ($request->wantsJson()) {
            return response()->json([
                'cart' => $cart
            ]);
        }

        return view('website.cart.index', compact('cart'));
    }

    /**
     * Add an item to the cart.
     */
    public function store(Request $request)
    {
        if (!auth('customer')->check()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'redirect' => route('website.login'),
                    'message' => 'Please login to add products'
                ], 401);
            }
            return redirect()->route('website.login');
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);
        $user = auth('customer')->user();
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

    /**
     * Update cart item quantity.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cartItem = CartItem::whereHas('cart', function($q) {
            $q->where('user_id', auth()->id());
        })->findOrFail($id);

        $product = $cartItem->product;
        $cartItem->quantity = $request->quantity;
        $cartItem->total = $cartItem->quantity * $product->price;
        $cartItem->save();

        // Update Cart Total
        $cart = $cartItem->cart;
        $subtotal = $cart->items()->sum('total');
        
        // Recalculate Discount
        $discount = 0;
        if ($cart->coupon_code === 'SUMMER20') {
            $discount = $subtotal * 0.20;
        }
        
        $cart->update([
            'discount_amount' => $discount,
            'total_amount' => $subtotal - $discount
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cart updated!',
            'cart_total' => number_format($cart->total_amount, 2),
            'cart_subtotal' => number_format($subtotal, 2),
            'discount' => number_format($discount, 2),
            'item_total' => number_format($cartItem->total, 2)
        ]);
    }

    /**
     * Remove item from cart.
     */
    public function destroy($id)
    {
        $cartItem = CartItem::whereHas('cart', function($q) {
            $q->where('user_id', auth()->id());
        })->findOrFail($id);

        $cart = $cartItem->cart;
        $cartItem->delete();

        // Update Cart Total
        $subtotal = $cart->items()->sum('total');
        
        // Recalculate Discount
        $discount = 0;
        if ($cart->coupon_code === 'SUMMER20') {
            $discount = $subtotal * 0.20;
        }

        $cart->update([
            'discount_amount' => $discount,
            'total_amount' => $subtotal - $discount
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Item removed!',
            'cart_count' => $cart->items()->count(),
            'cart_total' => number_format($cart->total_amount, 2),
            'cart_subtotal' => number_format($subtotal, 2),
            'discount' => number_format($discount, 2)
        ]);
    }

    /**
     * Apply a coupon to the cart.
     */
    public function applyCoupon(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $cart = Cart::where('user_id', auth()->id())
            ->where('status', 'active')
            ->first();

        if (!$cart) {
            return response()->json(['success' => false, 'message' => 'Cart not found'], 404);
        }

        // Demonstration logic for "SUMMER20" (20% off)
        $code = strtoupper($request->code);
        $discount = 0;
        $message = "Invalid coupon code";

        if ($code === 'SUMMER20') {
            $discount = $cart->items()->sum('total') * 0.20;
            $cart->update([
                'coupon_code' => $code,
                'discount_amount' => $discount
            ]);
            $message = "Coupon applied! You saved $" . number_format($discount, 2);
        } else {
            $cart->update([
                'coupon_code' => null,
                'discount_amount' => 0
            ]);
        }

        $newTotal = $cart->items()->sum('total') - $cart->discount_amount;
        $cart->update(['total_amount' => $newTotal]);

        return response()->json([
            'success' => $discount > 0,
            'message' => $message,
            'discount' => number_format($cart->discount_amount, 2),
            'total' => number_format($cart->total_amount, 2)
        ]);
    }
}
