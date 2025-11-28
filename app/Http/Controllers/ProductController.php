<?php

namespace App\Http\Controllers;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        // Select specific fields if you don't want to send everything
        return response()->json(
            Product::where('is_available', 1)->get()
        );
    }
    
    // Optional: Get Single Product
    public function show($id)
    {
        return response()->json(
            Product::where('product_id', $id)->firstOrFail()
        );
    }
}
