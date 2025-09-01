<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InventoryController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index()
    {
        $products = Inventory::all();

  return Inertia::render('inventory/InventoryLayout', [
    'products' => $products,
]);

    }

    /**
     * Store a newly created product.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string|max:255',
            'sku' => 'required|string|max:50|unique:inventories,sku',
            'stock' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
        ]);

        $inventory = Inventory::create($request->all());

        return response()->json([
            'message' => 'Product created successfully',
            'product' => $inventory
        ], 201);
    }

    /**
     * Display the specified product.
     */
    public function show($id)
    {
        $product = Inventory::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json($product);
    }

    /**
     * Update the specified product.
     */
 public function update(Request $request, Inventory $inventory)
{
    $inventory->update($request->only(['product_name', 'sku', 'stock', 'price']));

    return Inertia::render('inventory/InventoryLayout', [
        'products' => Inventory::all(),
        'success' => 'Product updated successfully',
    ]);
}
    /**
     * Remove the specified product.
     */
    public function destroy($id)
    {
        $product = Inventory::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }
public function scan(Request $request)
{
    $product = Inventory::where('sku', $request->barcode)->first();

    if ($product) {
        // Maybe add to cart directly, or just return updated inventory
        return redirect()->route('inventory.index')
            ->with('success', "{$product->product_name} added from scan!");
    }

    return redirect()->route('inventory.index')
        ->with('error', "No product found for barcode {$request->barcode}");
}


}