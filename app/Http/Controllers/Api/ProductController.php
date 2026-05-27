<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // List active products (supports ?search=, ?category_id=, ?low_stock=)
    public function index(Request $request)
    {
        $query = Product::where('is_active', true)->with('categoryRelation');

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('product_code', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if ($categoryId = $request->query('category_id')) {
            $query->where('category_id', $categoryId);
        }

        // Optional: filter by low stock
        if ($request->boolean('low_stock')) {
            $query->whereColumn('current_stock', '<=', 'reorder_level');
        }

        $products = $query->orderBy('product_code')->get();

        $activeQuery = Product::where('is_active', true);

        return response()->json([
            'success' => true,
            'message' => 'Products retrieved successfully.',
            'count' => $products->count(),
            'next_code' => 'P' . str_pad(Product::count() + 1, 3, '0', STR_PAD_LEFT),
            'meta' => [
                'total_items' => (clone $activeQuery)->count(),
                'low_stock_count' => (clone $activeQuery)->whereRaw('current_stock <= reorder_level')->count(),
                'total_stock_qty' => (float) (clone $activeQuery)->sum('current_stock'),
                'total_stock_val' => (float) ((clone $activeQuery)->selectRaw('SUM(buying_price * current_stock) as total_value')->value('total_value') ?? 0),
            ],
            'data' => $products,
        ], 200);
    }

    // Create a new product (Admin only)
    public function store(Request $request)
    {
        if (auth()->user()->role !== 'Admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized. Admin access required to add products.'], 403);
        }
        $validated = $request->validate([
            'product_code' => 'required|string|unique:products,product_code',
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'unit' => 'nullable|string|max:50',
            'buying_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'reorder_level' => 'nullable|numeric|min:0',
            'is_manufactured' => 'nullable|boolean',
        ]);

        $product = Product::create([
            'product_code' => $validated['product_code'],
            'name' => $validated['name'],
            'category' => $validated['category'] ?? '',
            'unit' => $validated['unit'] ?? 'Pcs',
            'buying_price' => $validated['buying_price'],
            'selling_price' => $validated['selling_price'],
            'reorder_level' => $validated['reorder_level'] ?? 0,
            'is_manufactured' => $request->boolean('is_manufactured'),
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully.',
            'data' => $product,
        ], 201);
    }

    // Get a single product's pricing and stock info
    public function show(string $id)
    {
        $product = Product::findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Product retrieved successfully.',
            'data' => [
                'id' => $product->id,
                'name' => $product->name,
                'selling_price' => $product->selling_price,
                'buying_price' => $product->buying_price,
                'current_stock' => $product->current_stock,
            ],
        ], 200);
    }

    // Update product details (Admin only)
    public function update(Request $request, string $id)
    {
        if (auth()->user()->role !== 'Admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized. Admin access required to edit products.'], 403);
        }
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'product_code' => 'sometimes|required|string|unique:products,product_code,' . $id,
            'name' => 'sometimes|required|string|max:255',
            'category' => 'nullable|string|max:255',
            'unit' => 'nullable|string|max:50',
            'buying_price' => 'sometimes|required|numeric|min:0',
            'selling_price' => 'sometimes|required|numeric|min:0',
            'reorder_level' => 'nullable|numeric|min:0',
            'is_manufactured' => 'nullable|boolean',
        ]);

        $product->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully.',
            'data' => $product->fresh(),
        ], 200);
    }

    // Soft-delete — sets is_active=false (Admin only)
    public function destroy(string $id)
    {
        if (auth()->user()->role !== 'Admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized. Admin access required to delete products.'], 403);
        }
        $product = Product::findOrFail($id);
        $product->update(['is_active' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Product deactivated successfully.',
        ], 200);
    }
}
