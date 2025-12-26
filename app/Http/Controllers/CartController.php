<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /** Get Cart Items */
    public function index(Request $request)
    {
        $cart = session()->get('cart', []);
        $items = [];
        $subtotal = 0;
        $totalItems = 0;

        foreach ($cart as $productId => $item) {
            $product = Product::with('category')->where('product_id', $productId)->first();
            
            if ($product && $product->is_available) {
                $itemData = [
                    'id' => $productId,
                    'name' => $product->name,
                    'price' => (float) $product->price,
                    'qty' => $item['quantity'],
                    'photo' => $product->photo,
                    'category' => $product->category->name ?? null,
                ];
                
                $items[] = $itemData;
                $subtotal += $itemData['price'] * $itemData['qty'];
                $totalItems += $itemData['qty'];
            }
        }

        return response()->json([
            'items' => $items,
            'totalItems' => $totalItems,
            'subtotal' => $subtotal,
        ]);
    }

    /** Add Item to Cart */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|string|size:5|exists:products,product_id',
            'quantity' => 'sometimes|integer|min:1',
        ]);

        $product = Product::where('product_id', $request->product_id)->firstOrFail();

        if (!$product->is_available) {
            return response()->json([
                'message' => 'This product is currently unavailable.'
            ], 400);
        }

        $quantity = $request->quantity ?? 1;
        $cart = session()->get('cart', []);

        if (isset($cart[$request->product_id])) {
            $cart[$request->product_id]['quantity'] += $quantity;
        } else {
            $cart[$request->product_id] = [
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $quantity,
                'photo' => $product->photo,
            ];
        }

        session()->put('cart', $cart);

        return response()->json([
            'message' => 'Item added to cart successfully',
            'cart' => $cart[$request->product_id],
        ], 201);
    }

    /** Update Cart Item */
    public function update(Request $request, $productId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = session()->get('cart', []);

        if (!isset($cart[$productId])) {
            return response()->json(['message' => 'Item not found in cart'], 404);
        }

        $cart[$productId]['quantity'] = $request->quantity;
        session()->put('cart', $cart);

        return response()->json([
            'message' => 'Cart updated',
            'cart' => $cart[$productId],
        ]);
    }

    /** Remove Item from Cart */
    public function destroy(Request $request, $productId)
    {
        $cart = session()->get('cart', []);

        if (!isset($cart[$productId])) {
            return response()->json(['message' => 'Item not found in cart'], 404);
        }

        unset($cart[$productId]);
        session()->put('cart', $cart);

        return response()->json(['message' => 'Item removed from cart']);
    }

    /** Clear Cart */
    public function clear(Request $request)
    {
        session()->forget('cart');

        return response()->json(['message' => 'Cart cleared']);
    }
}
