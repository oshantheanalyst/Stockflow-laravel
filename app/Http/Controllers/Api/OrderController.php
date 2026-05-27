<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // List all active orders with customer, user, and items
    public function index(Request $request)
    {
        $query = Order::with(['customer', 'user', 'items.product'])
            ->where('is_deleted', false);

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_no', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%")
                  ->orWhere('order_type', 'like', "%{$search}%");
            });
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        $orders = $query->orderByDesc('invoice_date')->get();

        return response()->json([
            'success'     => true,
            'message'     => 'Orders retrieved successfully.',
            'count'       => $orders->count(),
            'total_value' => $orders->sum('total'),
            'data'        => $orders,
        ], 200);
    }

    // Create a new order and deduct product stock
    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_no'       => 'required|string|unique:orders,invoice_no',
            'invoice_date'     => 'required|date',
            'customer_id'      => 'nullable|exists:customers,id',
            'payment_method'   => 'required|in:Cash,Credit,Cheque,Bill to Bill',
            'discount'         => 'nullable|numeric|min:0',
            'credit_days'      => 'nullable|integer|min:1',
            'cheque_number'    => 'nullable|string',
            'cheque_bank_name' => 'nullable|string',
            'cheque_due_date'  => 'nullable|date',
            'order_type'       => 'nullable|string',
            'status'           => 'nullable|string',
            'items'            => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty'        => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $order = DB::transaction(function () use ($validated) {
            $subtotal = 0;
            $itemsData = [];

            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $lineTotal = $item['qty'] * $item['unit_price'];
                $subtotal += $lineTotal;
                $itemsData[] = [
                    'product'    => $product,
                    'qty'        => $item['qty'],
                    'unit_price' => $item['unit_price'],
                    'line_total' => $lineTotal,
                ];
            }

            $discount = $validated['discount'] ?? 0;
            $total = $subtotal - $discount;
            
            $isPaid = $validated['payment_method'] === 'Cash';
            $status = $validated['status'] ?? ($isPaid ? 'Paid' : 'Pending');

            $dueDate = null;
            if ($validated['payment_method'] === 'Credit' && isset($validated['credit_days'])) {
                $dueDate = date('Y-m-d', strtotime($validated['invoice_date'] . ' + ' . $validated['credit_days'] . ' days'));
            } elseif ($validated['payment_method'] === 'Cheque') {
                $dueDate = $validated['cheque_due_date'] ?? null;
            }

            $order = Order::create([
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
                
                'date'               => $validated['invoice_date'],
                'status'             => $status,
                'order_type'         => $validated['order_type'] ?? 'Sales',
                'user_id'            => auth()->id(),
            ]);

            foreach ($itemsData as $item) {
                $order->items()->create([
                    'product_id'             => $item['product']->id,
                    'qty'                    => $item['qty'],
                    'unit_price_snapshot'    => $item['unit_price'],
                    'buying_price_snapshot'  => $item['product']->buying_price,
                    'line_total'             => $item['line_total'],
                    
                    'quantity'               => $item['qty'],
                    'subtotal'               => $item['line_total'],
                ]);

                Product::where('id', $item['product']->id)
                    ->decrement('current_stock', $item['qty']);
            }

            return $order->load('items', 'customer', 'user');
        });

        return response()->json([
            'success' => true,
            'message' => 'Order created successfully.',
            'data'    => $order,
        ], 201);
    }

    // Get a single order with detailed item breakdown
    public function show(string $id)
    {
        $order = Order::with(['customer', 'user', 'items.product'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Order details retrieved successfully.',
            'data'    => array_merge($order->toArray(), [
                'status'  => $order->status,
                'balance' => $order->balance,
                'profit'  => $order->profit,
            ]),
        ], 200);
    }

    // Update order payment status
    public function update(Request $request, string $id)
    {
        $order = Order::findOrFail($id);

        $validated = $request->validate([
            'status'      => 'sometimes|string',
            'is_paid'     => 'sometimes|boolean',
            'amount_paid' => 'sometimes|numeric|min:0',
        ]);

        $order->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Order updated successfully.',
            'data'    => $order->fresh()->load('items', 'customer', 'user'),
        ], 200);
    }

    // Soft-delete an order
    public function destroy(string $id)
    {
        $order = Order::findOrFail($id);
        $order->update(['is_deleted' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Order deleted successfully.',
        ], 200);
    }
}
