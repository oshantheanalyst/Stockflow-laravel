<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    // List all invoices (supports ?search= and ?status= filters)
    public function index(Request $request)
    {
        $query = Invoice::with('customer')
            ->where('is_deleted', false);

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_no', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($status = $request->query('status')) {
            if ($status === 'paid') {
                $query->where('is_paid', true);
            } elseif ($status === 'unpaid') {
                $query->where('is_paid', false);
            }
        }

        $invoices = $query->orderByDesc('invoice_date')->orderByDesc('id')->get();

        return response()->json([
            'success'     => true,
            'message'     => 'Sales retrieved successfully.',
            'count'       => $invoices->count(),
            'total_sales' => $invoices->sum('total'),
            'data'        => $invoices,
        ], 200);
    }

    // Return products, customers and next invoice number for the create form
    public function createForm()
    {
        $products = Product::where('is_active', true)->orderBy('name')->get(['id', 'name', 'selling_price', 'buying_price', 'current_stock']);
        $customers = Customer::where('is_active', true)->orderBy('name')->get(['id', 'name', 'phone', 'area']);

        $lastInvoice = Invoice::orderByDesc('id')->first();
        $nextNum = ($lastInvoice ? $lastInvoice->id : 0) + 1;
        $nextInvoiceNo = 'INV' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);

        return response()->json([
            'success' => true,
            'message' => 'Sale form data retrieved successfully.',
            'data' => [
                'products' => $products,
                'customers' => $customers,
                'next_invoice_no' => $nextInvoiceNo,
            ],
        ], 200);
    }

    // Create a new invoice and deduct stock for each line item
    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_no'     => 'required|string|unique:orders,invoice_no',
            'invoice_date'   => 'required|date',
            'customer_id'    => 'nullable|exists:customers,id',
            'payment_method' => 'required|in:Cash,Credit,Cheque',
            'discount'       => 'nullable|numeric|min:0',
            'credit_days'    => 'nullable|integer|min:1',
            'cheque_number'  => 'nullable|string',
            'cheque_bank_name' => 'nullable|string',
            'cheque_due_date'  => 'nullable|date',
            'items'          => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty'        => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $invoice = DB::transaction(function () use ($validated) {
            $subtotal = 0;
            $itemsData = [];

            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $lineTotal = $item['qty'] * $item['unit_price'];
                $subtotal += $lineTotal;
                $itemsData[] = [
                    'product'       => $product,
                    'qty'           => $item['qty'],
                    'unit_price'    => $item['unit_price'],
                    'line_total'    => $lineTotal,
                ];
            }

            $discount = $validated['discount'] ?? 0;
            $total = $subtotal - $discount;
            $isPaid = $validated['payment_method'] === 'Cash';

            $dueDate = null;
            if ($validated['payment_method'] === 'Credit' && isset($validated['credit_days'])) {
                $dueDate = date('Y-m-d', strtotime($validated['invoice_date'] . ' + ' . $validated['credit_days'] . ' days'));
            } elseif ($validated['payment_method'] === 'Cheque') {
                $dueDate = $validated['cheque_due_date'] ?? null;
            }

            $invoice = Invoice::create([
                'invoice_no'         => $validated['invoice_no'],
                'customer_id'        => $validated['customer_id'] ?? null,
                'invoice_date'       => $validated['invoice_date'],
                'subtotal'           => $subtotal,
                'discount'           => $discount,
                'total'              => $total,
                'amount_paid'        => $isPaid ? $total : 0,
                'payment_method'     => $validated['payment_method'],
                'is_paid'            => $isPaid,
                'due_date'           => $dueDate,
                'credit_period_days' => $validated['credit_days'] ?? null,
                'cheque_bank_name'   => $validated['cheque_bank_name'] ?? null,
                'cheque_number'      => $validated['cheque_number'] ?? null,
                'is_deleted'         => false,
            ]);

            foreach ($itemsData as $item) {
                $invoice->items()->create([
                    'product_id'             => $item['product']->id,
                    'qty'                    => $item['qty'],
                    'unit_price_snapshot'    => $item['unit_price'],
                    'buying_price_snapshot'  => $item['product']->buying_price,
                    'line_total'             => $item['line_total'],
                ]);

                // Deduct stock
                Product::where('id', $item['product']->id)
                    ->decrement('current_stock', $item['qty']);
            }

            return $invoice->load('items', 'customer');
        });

        return response()->json([
            'success' => true,
            'message' => 'Invoice created successfully.',
            'data'    => $invoice,
        ], 201);
    }

    // Get a single invoice with its items and computed fields
    public function show(string $id)
    {
        $invoice = Invoice::with(['customer', 'items.product'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Invoice retrieved successfully.',
            'data'    => array_merge($invoice->toArray(), [
                'status'  => $invoice->status,
                'balance' => $invoice->balance,
                'profit'  => $invoice->profit,
            ]),
        ], 200);
    }

    // Update payment status or amount paid
    public function update(Request $request, string $id)
    {
        $invoice = Invoice::findOrFail($id);

        $validated = $request->validate([
            'is_paid'     => 'sometimes|boolean',
            'amount_paid' => 'sometimes|numeric|min:0',
        ]);

        $invoice->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Invoice updated successfully.',
            'data'    => $invoice->fresh(),
        ], 200);
    }

    // Soft-delete — sets is_deleted flag instead of removing the row
    public function destroy(string $id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->update(['is_deleted' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Invoice deleted successfully.',
        ], 200);
    }
}
