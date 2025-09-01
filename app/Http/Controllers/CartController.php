<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Show the user's active cart.
     */
    public function index()
    {
        $cart = Auth::user()
            ->cart()
            ->with('cartItems.inventory')
            ->firstOrCreate(['active' => true]);

        return response()->json($cart);
    }

    /**
     * Add item to the cart.
     */
    public function add(Request $request)
    {
        $request->validate([
            'inventory_id' => 'required|exists:inventories,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = Auth::user()->cart()->firstOrCreate(['active' => true]);

        $cart->cartItems()->create([
            'inventory_id' => $request->inventory_id,
            'quantity' => $request->quantity,
        ]);

        return response()->json([
            'message' => 'Item added to cart',
            'cart' => $cart->load('cartItems.inventory'),
        ]);
    }

    /**
     * Remove item from the cart.
     */
    public function remove($itemId)
    {
        $cart = Auth::user()->cart()->first();

        if (!$cart) {
            return response()->json(['message' => 'Cart not found'], 404);
        }

        $cartItem = $cart->cartItems()->where('id', $itemId)->first();

        if (!$cartItem) {
            return response()->json(['message' => 'Item not found in cart'], 404);
        }

        $cartItem->delete();

        return response()->json([
            'message' => 'Item removed from cart',
            'cart' => $cart->load('cartItems.inventory'),
        ]);
    }

    /**
     * Clear the entire cart.
     */
    public function clear()
    {
        $cart = Auth::user()->cart()->first();

        if (!$cart) {
            return response()->json(['message' => 'Cart not found'], 404);
        }

        $cart->cartItems()->delete();

        return response()->json([
            'message' => 'Cart cleared',
            'cart' => $cart->load('cartItems.inventory'),
        ]);
    }
}