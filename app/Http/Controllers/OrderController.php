<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Inventory;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class OrderController extends Controller
{
    /**
     * Show current cart.
     * 
     * Retrieves the authenticated user's active cart along with its items and inventory details.
     * If no active cart exists, creates a new one.
     */
    public function cart()
    {
        $cart = Auth::user()
            ->cart()
            ->with('cartItems.inventory')
            ->firstOrCreate(['active' => true]);

        return response()->json($cart);
    }
  public function dashboard()
    {
        $products = Inventory::all();

        return Inertia::render('orders/OrderingDashboard', [
            'products' => $products,
        ]);
    }
    /**
     * Add item to cart.
     * 
     * Validates the request for inventory ID and quantity, then adds the item to the user's active cart.
     * Returns the updated cart with items and inventory details.
     */
    public function addToCart(Request $request)
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
     * Checkout: convert cart → order.
     * 
     * Converts the user's active cart into an order.
     * - Checks if the cart exists and is not empty.
     * - Creates an order with the total amount.
     * - Moves cart items to the order and deducts inventory stock.
     * - Deactivates the cart.
     * Returns the created order with its items and inventory details.
     */
    public function checkout()
    {
        $user = Auth::user();
        $cart = $user->cart()->with('cartItems.inventory')->first();

        if (!$cart || $cart->cartItems->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 400);
        }

        // Create order
        $order = $user->orders()->create([
            'status' => 'pending',
            'total_amount' => $cart->cartItems->sum(fn($i) => $i->quantity * $i->inventory->price),
        ]);

        // Move items to order + deduct stock
        foreach ($cart->cartItems as $cartItem) {
            $order->items()->create([
                'inventory_id' => $cartItem->inventory_id,
                'quantity' => $cartItem->quantity,
                'price' => $cartItem->inventory->price,
            ]);

            $cartItem->inventory->decrement('stock', $cartItem->quantity);
        }

        // Deactivate cart
        $cart->update(['active' => false]);

        return response()->json([
            'message' => 'Checkout completed',
            'order' => $order->load('items.inventory'),
        ]);
    }
}