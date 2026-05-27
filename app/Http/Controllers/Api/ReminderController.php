<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\SupplierPayment;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    // List all active reminders (unpaid invoices and pending supplier payments)
    public function index(Request $request)
    {
        $reminders = collect();
        $invoices = Invoice::with('customer')
            ->where('is_deleted', false)
            ->where('is_paid', false)
            ->whereNotNull('due_date')
            ->get();

        foreach ($invoices as $inv) {
            $reminders->push([
                'id'          => $inv->id,
                'type'        => 'Invoice',
                'reference'   => $inv->invoice_no,
                'name'        => $inv->customer->name ?? 'Walk-in Customer',
                'amount'      => (float) $inv->total - (float) $inv->amount_paid,
                'due_date'    => $inv->due_date?->format('Y-m-d'),
                'days_overdue' => $inv->due_date ? (int) now()->diffInDays($inv->due_date, false) : 0,
                'status'      => ($inv->due_date && $inv->due_date->lt(now())) ? 'Overdue' : 'Pending',
            ]);
        }

        $payments = SupplierPayment::with('supplier')
            ->where('status', 'Pending')
            ->whereNotNull('due_date')
            ->get();

        foreach ($payments as $sp) {
            $reminders->push([
                'id'          => $sp->id,
                'type'        => 'Supplier',
                'reference'   => $sp->description,
                'name'        => $sp->supplier->name ?? 'Unknown Supplier',
                'amount'      => (float) $sp->amount,
                'due_date'    => $sp->due_date?->format('Y-m-d'),
                'days_overdue' => $sp->due_date ? (int) now()->diffInDays($sp->due_date, false) : 0,
                'status'      => ($sp->due_date && $sp->due_date->lt(now())) ? 'Overdue' : 'Pending',
            ]);
        }

        $sorted = $reminders->sortBy('due_date')->values();

        return response()->json([
            'success'         => true,
            'message'         => 'Reminders retrieved successfully.',
            'count'           => $sorted->count(),
            'overdue_count'   => $sorted->where('status', 'Overdue')->count(),
            'data'            => $sorted,
        ], 200);
    }

    // Reminders are auto-generated, so manual creation is disabled
    public function store(Request $request)
    {
        return response()->json([
            'success' => false,
            'message' => 'Reminders are automatically generated from unpaid invoices and supplier payments.',
        ], 422);
    }

    // Fetch the underlying invoice or supplier payment for a reminder
    public function show(string $id)
    {
        // Invoice first, then supplier payment
        $invoice = Invoice::with('customer')->where('id', $id)->where('is_paid', false)->first();
        if ($invoice) {
            return response()->json([
                'success' => true,
                'message' => 'Reminder retrieved.',
                'type'    => 'Invoice',
                'data'    => $invoice,
            ]);
        }

        $payment = SupplierPayment::with('supplier')->where('id', $id)->where('status', 'Pending')->first();
        if ($payment) {
            return response()->json([
                'success' => true,
                'message' => 'Reminder retrieved.',
                'type'    => 'Supplier',
                'data'    => $payment,
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Reminder not found.'], 404);
    }

    // Mark a reminder as complete (pays the invoice or supplier payment)
    public function update(Request $request, string $id)
    {
        $request->validate([
            'type' => 'required|in:Invoice,Supplier',
        ]);

        if ($request->type === 'Invoice') {
            $invoice = Invoice::findOrFail($id);
            $invoice->update(['is_paid' => true, 'amount_paid' => $invoice->total]);
            return response()->json([
                'success' => true,
                'message' => 'Invoice marked as paid.',
            ]);
        } else {
            $payment = SupplierPayment::findOrFail($id);
            $payment->update(['status' => 'Paid', 'paid_on' => now()]);
            return response()->json([
                'success' => true,
                'message' => 'Supplier payment marked as paid.',
            ]);
        }
    }

    // Reminders cannot be manually deleted
    public function destroy(string $id)
    {
        return response()->json([
            'success' => false,
            'message' => 'Reminders cannot be manually deleted. Resolve the underlying invoice or payment instead.',
        ], 422);
    }
}
