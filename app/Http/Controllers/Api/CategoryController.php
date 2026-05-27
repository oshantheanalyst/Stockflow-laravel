<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // List all categories
    public function index()
    {
        $categories = Category::orderBy('category_name')->get();

        return response()->json([
            'success' => true,
            'message' => 'Categories retrieved successfully.',
            'count'   => $categories->count(),
            'data'    => $categories,
        ], 200);
    }

    // Create a new category
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_name' => 'required|string|unique:categories,category_name|max:255',
        ]);

        $category = Category::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully.',
            'data'    => $category,
        ], 201);
    }

    // Get a category and its related products
    public function show(string $id)
    {
        $category = Category::with('products')->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Category details retrieved successfully.',
            'data'    => $category,
        ], 200);
    }

    // Update category details
    public function update(Request $request, string $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'category_name' => 'required|string|max:255|unique:categories,category_name,' . $id,
        ]);

        $category->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully.',
            'data'    => $category,
        ], 200);
    }

    // Delete a category (related products will have category_id set to null)
    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully.',
        ], 200);
    }
}
