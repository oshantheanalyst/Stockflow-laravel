<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    // List all active suppliers
    public function index(Request $request)
    {
        $query = Supplier::where('is_active', true);

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('supplier_code', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        $suppliers = $query->orderBy('supplier_code')->get();

        return response()->json([
            'success' => true,
            'message' => 'Suppliers retrieved successfully.',
            'count'   => $suppliers->count(),
            'next_code' => 'S' . str_pad(Supplier::count() + 1, 3, '0', STR_PAD_LEFT),
            'data'    => $suppliers,
        ], 200);
    }

    // Create a new supplier
    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_code'  => 'required|string|unique:suppliers,supplier_code',
            'name'           => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone'          => 'nullable|string|max:50',
            'category'       => 'nullable|string|max:255',
            'terms_days'     => 'nullable|integer|min:0',
        ]);

        $supplier = Supplier::create(array_merge($validated, ['is_active' => true]));

        return response()->json([
            'success' => true,
            'message' => 'Supplier created successfully.',
            'data'    => $supplier,
        ], 201);
    }

    // Get a single supplier and their payments
    public function show(string $id)
    {
        $supplier = Supplier::with('payments')->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Supplier retrieved successfully.',
            'data'    => array_merge($supplier->toArray(), [
                'total_owed'    => $supplier->total_owed,
                'total_paid'    => $supplier->total_paid,
                'balance'       => $supplier->balance,
                'pending_count' => $supplier->pending_count,
            ]),
        ], 200);
    }

    // Update a supplier's information
    public function update(Request $request, string $id)
    {
        $supplier = Supplier::findOrFail($id);

        $validated = $request->validate([
            'supplier_code'  => 'sometimes|required|string|unique:suppliers,supplier_code,' . $id,
            'name'           => 'sometimes|required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone'          => 'nullable|string|max:50',
            'category'       => 'nullable|string|max:255',
            'terms_days'     => 'nullable|integer|min:0',
        ]);

        $supplier->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Supplier updated successfully.',
            'data'    => $supplier->fresh(),
        ], 200);
    }

    // Soft-delete a supplier
    public function destroy(string $id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->update(['is_active' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Supplier deactivated successfully.',
        ], 200);
    }

    // Get all payments for a specific supplier
    public function payments(string $id)
    {
        $supplier = Supplier::findOrFail($id);
        $payments = SupplierPayment::where('supplier_id', $id)
            ->orderByDesc('bill_date')
            ->get();

        return response()->json($payments, 200);
    }

    // Record a new payment or bill for a supplier
    public function storePayment(Request $request, string $id)
    {
        $request->validate([
            'description' => 'required|string',
            'amount'      => 'required|numeric|min:0',
            'bill_date'   => 'required|date',
            'method'      => 'required|string',
        ]);

        $dueDate = null;
        $reference = null;

        if ($request->input('method') === 'Credit' && $request->credit_days) {
            $dueDate = date('Y-m-d', strtotime($request->bill_date . ' + ' . $request->credit_days . ' days'));
        } elseif ($request->input('method') === 'Cheque') {
            $reference = $request->cheque_number;
            $dueDate = $request->cheque_due_date;
        }

        $payment = SupplierPayment::create([
            'supplier_id' => $id,
            'description' => $request->description,
            'amount'      => $request->amount,
            'bill_date'   => $request->bill_date,
            'due_date'    => $dueDate,
            'method'      => $request->input('method'),
            'reference'   => $reference,
            'status'      => 'Pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment added successfully.',
            'data'    => $payment,
        ], 201);
    }

    // Mark a pending supplier payment as paid
    public function markPaid(string $id)
    {
        $payment = SupplierPayment::findOrFail($id);
        $payment->update([
            'status'  => 'Paid',
            'paid_on' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment marked as paid successfully.',
            'data'    => $payment,
        ], 200);
    }

    // Delete a supplier payment record
    public function destroyPayment(string $id)
    {
        $payment = SupplierPayment::findOrFail($id);
        $payment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Payment deleted successfully.',
        ], 200);
    }
}
