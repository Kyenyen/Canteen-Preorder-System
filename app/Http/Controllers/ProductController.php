<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File; 

class ProductController extends Controller
{
    /** Get All Products */
    public function index(Request $request)
    {
        $query = Product::with('category'); 

        if ($request->has('all') && $request->user() && $request->user()->role === 'admin') {
            return response()->json($query->get());
        }

        return response()->json(
            $query->where('is_available', 1)->get()
        );
    }

    /** Get Single Product */
    public function show($id)
    {
        return response()->json(
            Product::with('category')->where('product_id', $id)->firstOrFail()
        );
    }

    /** Create Product */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|string|size:5|exists:categories,category_id',
            'price' => 'required|numeric|min:0',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg',
            'description' => 'required|string',
            'is_available' => 'boolean'
        ], [
            'name.required' => 'Please enter the product name.',
            'category_id.required' => 'Please select a category ID.',
            'category_id.size' => 'Category ID must be 5 characters long.',
            'category_id.exists' => 'The selected category ID is invalid.',
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
            $file->move(public_path('products'), $filename);
            $photoPath = 'products/' . $filename;
        }

        $product = Product::create([
            'product_id' => $newId,
            'name' => $request->name,
            'category_id' => $request->category_id, 
            'price' => $request->price,
            'photo' => $photoPath,
            'description' => $request->description,
            'is_available' => filter_var($request->is_available, FILTER_VALIDATE_BOOLEAN),
        ]);

        // Increment the category's product count
        $category = \App\Models\Category::where('category_id', $request->category_id)->first();
        if ($category) {
            $category->increment('quantity');
        }

        $product->load('category');

        return response()->json(['message' => 'Product created', 'product' => $product], 201);
    }

    /** Update Product */
    public function updateProduct(Request $request, $id)
    {
        $product = Product::where('product_id', $id)->firstOrFail();

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'category_id' => 'sometimes|string|size:5|exists:categories,category_id', 
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

        if ($request->hasFile('photo')) {
            if ($product->photo && file_exists(public_path($product->photo))) {
                unlink(public_path($product->photo));
            }
            $file = $request->file('photo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('products'), $filename);
            $product->photo = 'products/' . $filename;
        }

        if ($request->has('name')) $product->name = $request->name;
        
        // Handle category change with count updates
        if ($request->has('category_id') && $request->category_id !== $product->category_id) {
            $oldCategoryId = $product->category_id;
            $newCategoryId = $request->category_id;
            
            // Decrement old category count
            $oldCategory = \App\Models\Category::where('category_id', $oldCategoryId)->first();
            if ($oldCategory && $oldCategory->quantity > 0) {
                $oldCategory->decrement('quantity');
            }
            
            // Increment new category count
            $newCategory = \App\Models\Category::where('category_id', $newCategoryId)->first();
            if ($newCategory) {
                $newCategory->increment('quantity');
            }
            
            $product->category_id = $newCategoryId;
        }
        
        if ($request->has('price')) $product->price = $request->price;
        if ($request->has('description')) $product->description = $request->description;
        if ($request->has('is_available')) {
             $product->is_available = filter_var($request->is_available, FILTER_VALIDATE_BOOLEAN);
        }

        $product->save();
        $product->load('category');

        return response()->json(['message' => 'Product updated', 'product' => $product]);
    }

    /** Delete Product */
    public function destroy($id)
    {
        $product = Product::where('product_id', $id)->firstOrFail();

        // Store the category_id before deleting
        $categoryId = $product->category_id;

        if ($product->photo && file_exists(public_path($product->photo))) {
            unlink(public_path($product->photo));
        }

        $product->delete();

        // Decrement the category's product count
        $category = \App\Models\Category::where('category_id', $categoryId)->first();
        if ($category && $category->quantity > 0) {
            $category->decrement('quantity');
        }

        return response()->json(['message' => 'Product deleted']);
    }
}