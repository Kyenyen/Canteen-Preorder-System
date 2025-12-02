<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;

class CategoryController extends Controller
{
    /**
     * 1. Get All Categories.
     */
    public function index()
    {
        // Retrieve all categories, perhaps ordered by name
        return response()->json(Category::orderBy('name')->get());
    }

    /**
     * 2. Get Single Category by ID.
     */
    public function show($id)
    {
        return response()->json(
            Category::where('category_id', $id)->firstOrFail()
        );
    }

    /**
     * 3. Create New Category (Admin).
     */
    public function store(Request $request)
    {
        // 3.1 Validation: Only the name is required from the user.
        $request->validate([
            'name' => 'required|string|max:100|unique:categories,name',
        ], [
            'name.required' => 'The category name is required.',
            'name.unique' => 'A category with this name already exists.',
        ]);

        // 3.2 ID Generation (similar logic to Product ID): C0001, C0002, etc.
        $lastCategory = Category::orderBy('category_id', 'desc')->first();
        
        if ($lastCategory) {
            $number = intval(substr($lastCategory->category_id, 1)) + 1;
        } else {
            $number = 1;
        }
        
        $newId = 'C' . str_pad($number, 4, '0', STR_PAD_LEFT);

        // 3.3 Create the category
        $category = Category::create([
            'category_id' => $newId,
            'name' => $request->name,
            'quantity' => 0,
        ]);

        return response()->json(['message' => 'Category created successfully', 'category' => $category], 201);
    }

    /**
     * 4. Update Category (Admin).
     */
    public function updateCategory(Request $request, $id)
    {
        $category = Category::where('category_id', $id)->firstOrFail();

        // 4.1 Validation: Check if name is provided and ensure uniqueness (excluding the current category)
        $request->validate([
            'name' => 'sometimes|string|max:100|unique:categories,name,' . $category->category_id . ',category_id',
        ], [
            'name.unique' => 'A category with this name already exists.',
        ]);

        // 4.2 Update fields if present
        if ($request->has('name')) {
            $category->name = $request->name;
        }
        
        // Note: category_id and quantity are generally not updated via this route.

        $category->save();

        return response()->json(['message' => 'Category updated successfully', 'category' => $category]);
    }

    /**
     * 5. Delete Category (Admin).
     */
    public function destroy($id)
    {
        $category = Category::where('category_id', $id)->firstOrFail();

        try {
            $category->delete();
            return response()->json(['message' => 'Category deleted successfully']);
        } catch (QueryException $e) {
            // This catches the integrity constraint violation (SQLSTATE[23000]) 
            // if products are still linked to this category.
            return response()->json([
                'message' => 'Cannot delete category. Products are still associated with it. Please reassign the products first.'
            ], 409); // 409 Conflict
        }
    }
}