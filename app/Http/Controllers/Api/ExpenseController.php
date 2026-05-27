<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    // List expenses (supports ?search=, ?from=, ?to= filters)
    public function index(Request $request)
    {
        $query = Expense::query();

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('category', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($from = $request->query('from')) {
            $query->where('date', '>=', $from);
        }

        if ($to = $request->query('to')) {
            $query->where('date', '<=', $to);
        }

        $expenses = $query->orderByDesc('date')->orderBy('category')->get();

        return response()->json([
            'success'     => true,
            'message'     => 'Expenses retrieved successfully.',
            'count'       => $expenses->count(),
            'total_amount' => $expenses->sum('amount'),
            'meta' => [
                'total_expenses_val' => (float) Expense::sum('amount'),
                'expense_count' => Expense::count(),
                'average_expense_val' => (float) (Expense::avg('amount') ?? 0),
            ],
            'data'        => $expenses,
        ], 200);
    }

    // Create a new expense record
    public function store(Request $request)
    {
        $validated = $request->validate([
            'date'        => 'required|date',
            'category'    => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount'      => 'required|numeric|min:0',
        ]);

        $expense = Expense::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Expense created successfully.',
            'data'    => $expense,
        ], 201);
    }

    // Get a single expense record
    public function show(string $id)
    {
        $expense = Expense::findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Expense retrieved successfully.',
            'data'    => $expense,
        ], 200);
    }

    // Update an expense
    public function update(Request $request, string $id)
    {
        $expense = Expense::findOrFail($id);

        $validated = $request->validate([
            'date'        => 'sometimes|required|date',
            'category'    => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'amount'      => 'sometimes|required|numeric|min:0',
        ]);

        $expense->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Expense updated successfully.',
            'data'    => $expense->fresh(),
        ], 200);
    }

    // Delete an expense record
    public function destroy(string $id)
    {
        $expense = Expense::findOrFail($id);
        $expense->delete();

        return response()->json([
            'success' => true,
            'message' => 'Expense deleted successfully.',
        ], 200);
    }
}
