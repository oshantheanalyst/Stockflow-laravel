<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use \App\Traits\BelongsToTenant;

    protected $table = 'orders';

    protected $fillable = [
        'invoice_no',
        'customer_id',
        'invoice_date',
        'subtotal',
        'discount',
        'total',
        'amount_paid',
        'is_deleted',
        'payment_method',
        'is_paid',
        'due_date',
        'credit_period_days',
        'cheque_bank_name',
        'cheque_number',
        
        // ERD Fields
        'date',
        'status',
        'order_type',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'invoice_date' => 'date',
            'due_date' => 'date',
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'total' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'is_deleted' => 'boolean',
            'is_paid' => 'boolean',
            'credit_period_days' => 'integer',
            
            // ERD Field casts
            'date' => 'date',
        ];
    }

    protected static function booted()
    {
        static::saving(function ($order) {
            // Sync ERD Date and Invoice Date
            if ($order->isDirty('invoice_date') && !$order->isDirty('date')) {
                $order->date = $order->invoice_date;
            } elseif ($order->isDirty('date') && !$order->isDirty('invoice_date')) {
                $order->invoice_date = $order->date;
            }

            // Sync ERD Status and Invoice is_paid
            if ($order->isDirty('is_paid') && !$order->isDirty('status')) {
                $order->status = $order->is_paid ? 'Paid' : 'Pending';
            } elseif ($order->isDirty('status') && !$order->isDirty('is_paid')) {
                $order->is_paid = strtolower($order->status) === 'paid';
            }

            // Ensure user_id is populated if not set
            if (empty($order->user_id) && auth()->check()) {
                $order->user_id = auth()->id();
            }
        });
    }

    // ── Relationships ─────────────────────────────────────

    // The user who processed this order (User processes Order in ERD).
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // The customer this order belongs to.
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Line items on this order (consists of Order_item in ERD).
    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    // Products on this order (Includes relationship in ERD).
    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_items', 'order_id', 'product_id')
                    ->withPivot('qty', 'unit_price_snapshot', 'buying_price_snapshot', 'line_total', 'quantity', 'subtotal')
                    ->withTimestamps();
    }



    // ── Scopes ────────────────────────────────────────────

    // Scope: only non-deleted orders.
    public function scopeActive($query)
    {
        return $query->where('is_deleted', false);
    }

    // Scope: only unpaid orders.
    public function scopeUnpaid($query)
    {
        return $query->where('is_paid', false);
    }

    // Scope: only paid orders.
    public function scopePaid($query)
    {
        return $query->where('is_paid', true);
    }

    // Scope: overdue orders.
    public function scopeOverdue($query)
    {
        return $query->where('is_paid', false)
                     ->whereNotNull('due_date')
                     ->where('due_date', '<', now());
    }

    // ── Accessors ─────────────────────────────────────────

    // Outstanding balance.
    public function getBalanceAttribute(): float
    {
        return (float) $this->total - (float) $this->amount_paid;
    }

    // Total profit on this order.
    public function getProfitAttribute(): float
    {
        return (float) $this->items->reduce(function (float $carry, OrderItem $item) {
            return $carry + ($item->unit_price_snapshot - $item->buying_price_snapshot) * ($item->quantity ?: $item->qty);
        }, 0.0);
    }
}
