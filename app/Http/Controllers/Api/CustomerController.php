<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    // List active customers with their total purchase amounts
    public function index(Request $request)
    {
        $query = Customer::where('is_active', true);

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('customer_code', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $customers = $query->orderBy('customer_code')->get();

        $totals = Invoice::where('is_deleted', false)
            ->whereNotNull('customer_id')
            ->selectRaw('customer_id, SUM(total) as total_bought')
            ->groupBy('customer_id')
            ->pluck('total_bought', 'customer_id');

        return response()->json([
            'success' => true,
            'message' => 'Customers retrieved successfully.',
            'count'   => $customers->count(),
            'next_code' => 'C' . str_pad(Customer::count() + 1, 3, '0', STR_PAD_LEFT),
            'totals'  => $totals,
            'data'    => $customers,
        ], 200);
    }

    // Create a new customer (auto-generates customer_code if not provided)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_code' => 'sometimes|required|string|unique:customers,customer_code',
            'name'          => 'required|string|max:255',
            'phone'         => 'nullable|string|max:50',
            'area'          => 'nullable|string|max:255',
            'address'       => 'nullable|string',
            'notes'         => 'nullable|string',
        ]);

        if (empty($validated['customer_code'])) {
            $nextId = Customer::max('id') + 1;
            $validated['customer_code'] = 'C' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
        }

        $customer = Customer::create(array_merge($validated, ['is_active' => true]));

        return response()->json([
            'success' => true,
            'message' => 'Customer created successfully.',
            'data'    => $customer,
        ], 201);
    }

    // Get a single customer with invoice history and balance
    public function show(string $id)
    {
        $customer = Customer::with(['invoices' => function ($q) {
            $q->where('is_deleted', false)->latest('invoice_date');
        }])->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Customer retrieved successfully.',
            'data'    => array_merge($customer->toArray(), [
                'total_bought'      => $customer->total_bought,
                'total_outstanding' => $customer->total_outstanding,
                'invoice_count'     => $customer->invoice_count,
            ]),
        ], 200);
    }

    // Update customer details
    public function update(Request $request, string $id)
    {
        $customer = Customer::findOrFail($id);

        $validated = $request->validate([
            'customer_code' => 'sometimes|required|string|unique:customers,customer_code,' . $id,
            'name'          => 'sometimes|required|string|max:255',
            'phone'         => 'nullable|string|max:50',
            'area'          => 'nullable|string|max:255',
            'address'       => 'nullable|string',
            'notes'         => 'nullable|string',
        ]);

        $customer->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Customer updated successfully.',
            'data'    => $customer->fresh(),
        ], 200);
    }

    // Soft-delete — sets is_active=false
    public function destroy(string $id)
    {
        $customer = Customer::findOrFail($id);
        $customer->update(['is_active' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Customer deactivated successfully.',
        ], 200);
    }
}
