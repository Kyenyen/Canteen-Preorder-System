<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // 1. Get All Products (Public/Student)
    public function index()
    {
        // Return only available products for the menu
        return response()->json(
            Product::where('is_available', 1)->get()
        );
    }

    // 2. Get Single Product (Public/Admin)
    public function show($id)
    {
        return response()->json(
            Product::where('product_id', $id)->firstOrFail()
        );
    }

    // 3. Create New Product (Admin)
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string',
            'is_available' => 'boolean'
        ]);

        // A. Generate Custom ID (e.g., P0001)
        $lastProduct = Product::orderBy('product_id', 'desc')->first();
        
        if ($lastProduct) {
            // Extract number from P0005 -> 5
            $number = intval(substr($lastProduct->product_id, 1)) + 1;
        } else {
            $number = 1;
        }
        
        // Pad with zeros: P0001
        $newId = 'P' . str_pad($number, 4, '0', STR_PAD_LEFT);

        // B. Handle Photo Upload
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('products', 'public');
        }

        // C. Create Product
        $product = Product::create([
            'product_id' => $newId,
            'name' => $request->name,
            'price' => $request->price,
            'photo' => $photoPath,
            'description' => $request->description,
            'is_available' => $request->has('is_available') ? $request->is_available : true,
        ]);

        return response()->json(['message' => 'Product created', 'product' => $product], 201);
    }

    // 4. Update Product (Admin)
    public function update(Request $request, $id)
    {
        $product = Product::where('product_id', $id)->firstOrFail();

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'price' => 'sometimes|numeric|min:0',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string',
            'is_available' => 'boolean'
        ]);

        // Handle Photo Replacement
        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($product->photo && Storage::disk('public')->exists($product->photo)) {
                Storage::disk('public')->delete($product->photo);
            }
            // Store new
            $path = $request->file('photo')->store('products', 'public');
            $product->photo = $path;
        }

        // Update fields if present
        if ($request->has('name')) $product->name = $request->name;
        if ($request->has('price')) $product->price = $request->price;
        if ($request->has('description')) $product->description = $request->description;
        if ($request->has('is_available')) $product->is_available = $request->is_available;

        $product->save(); // Save because custom keys sometimes behave oddly with update() array

        return response()->json(['message' => 'Product updated', 'product' => $product]);
    }

    // 5. Delete Product (Admin)
    public function destroy($id)
    {
        $product = Product::where('product_id', $id)->firstOrFail();

        // Delete photo from storage
        if ($product->photo && Storage::disk('public')->exists($product->photo)) {
            Storage::disk('public')->delete($product->photo);
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted']);
    }
}