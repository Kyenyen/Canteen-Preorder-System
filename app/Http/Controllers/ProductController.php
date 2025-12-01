<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File; // Required for unlink/file operations

class ProductController extends Controller
{
    // 1. Get All Products
    public function index(Request $request)
    {
        // Check if the user is an Admin asking for all items
        if ($request->has('all') && $request->user() && $request->user()->role === 'admin') {
            return response()->json(Product::all());
        }

        // Default: Only show available items
        return response()->json(
            Product::where('is_available', 1)->get()
        );
    }

    // 2. Get Single Product
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
            'category' => 'required|string|in:Breakfast,Lunch,Beverage', // Added Category
            'price' => 'required|numeric|min:0',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg',
            'description' => 'required|string',
            'is_available' => 'boolean'
        ], [
            'name.required' => 'Please enter the product name.',
            'category.required' => 'Please select a category.',
            'category.in' => 'Invalid category selected.',
            'price.required' => 'Please enter the price.',
            'price.numeric' => 'Price must be a number.',
            'price.min' => 'Price cannot be negative.',
            'photo.image' => 'The uploaded file must be an image.',
            'photo.mimes' => 'Only jpeg, png and jpg formats are allowed.',
            'description.required' => 'Please provide a product description.',
        ]);

        $lastProduct = Product::orderBy('product_id', 'desc')->first();
        
        if ($lastProduct) {
            $number = intval(substr($lastProduct->product_id, 1)) + 1;
        } else {
            $number = 1;
        }
        
        $newId = 'P' . str_pad($number, 4, '0', STR_PAD_LEFT);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time() . '_' . $file->getClientOriginalName();
            
            // Save to public/products
            $file->move(public_path('products'), $filename);
            $photoPath = 'products/' . $filename;
        }

        $product = Product::create([
            'product_id' => $newId,
            'name' => $request->name,
            'category' => $request->category, // Save Category
            'price' => $request->price,
            'photo' => $photoPath,
            'description' => $request->description,
            'is_available' => filter_var($request->is_available, FILTER_VALIDATE_BOOLEAN),
        ]);

        return response()->json(['message' => 'Product created', 'product' => $product], 201);
    }

    // 4. Update Product (Admin)
    public function updateProduct(Request $request, $id)
    {
        $product = Product::where('product_id', $id)->firstOrFail();

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'category' => 'sometimes|string|in:Breakfast,Lunch,Beverage', // Added Category Validation
            'price' => 'sometimes|numeric|min:0',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'sometimes|string',
            'is_available' => 'boolean'
        ], [
            'price.numeric' => 'Price must be a number.',
            'price.min' => 'Price cannot be negative.',
            'photo.image' => 'The uploaded file must be an image.',
            'photo.mimes' => 'Only jpeg, png, jpg, and gif formats are allowed.',
            'photo.max' => 'Image size must not exceed 2MB.',
        ]);

        // Handle Photo Replacement (FIXED: Uses public path logic to match store)
        if ($request->hasFile('photo')) {
            // Delete old photo if it exists in public folder
            if ($product->photo && file_exists(public_path($product->photo))) {
                unlink(public_path($product->photo));
            }
            
            // Store new photo
            $file = $request->file('photo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('products'), $filename);
            $product->photo = 'products/' . $filename;
        }

        // Update fields if present
        if ($request->has('name')) $product->name = $request->name;
        if ($request->has('category')) $product->category = $request->category; // Update Category
        if ($request->has('price')) $product->price = $request->price;
        if ($request->has('description')) $product->description = $request->description;
        if ($request->has('is_available')) {
             $product->is_available = filter_var($request->is_available, FILTER_VALIDATE_BOOLEAN);
        }

        $product->save();

        return response()->json(['message' => 'Product updated', 'product' => $product]);
    }

    // 5. Delete Product (Admin)
    public function destroy($id)
    {
        $product = Product::where('product_id', $id)->firstOrFail();

        // Delete photo from public folder (FIXED: Uses public path logic)
        if ($product->photo && file_exists(public_path($product->photo))) {
            unlink(public_path($product->photo));
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted']);
    }
}