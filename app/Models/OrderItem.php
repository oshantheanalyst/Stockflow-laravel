<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $table = 'order_items';

    protected $fillable = [
        'order_id',
        'invoice_id',
        'product_id',
        'qty',
        'unit_price_snapshot',
        'buying_price_snapshot',
        'line_total',
        
        // ERD fields
        'quantity',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'qty' => 'integer',
            'unit_price_snapshot' => 'decimal:2',
            'buying_price_snapshot' => 'decimal:2',
            'line_total' => 'decimal:2',
            
            // ERD field casts
            'quantity' => 'integer',
            'subtotal' => 'decimal:2',
        ];
    }

    protected static function booted()
    {
        static::saving(function ($item) {
            // Sync Quantity
            if ($item->isDirty('qty') && !$item->isDirty('quantity')) {
                $item->quantity = $item->qty;
            } elseif ($item->isDirty('quantity') && !$item->isDirty('qty')) {
                $item->qty = $item->quantity;
            }

            // Sync SubTotal
            if ($item->isDirty('line_total') && !$item->isDirty('subtotal')) {
                $item->subtotal = $item->line_total;
            } elseif ($item->isDirty('subtotal') && !$item->isDirty('line_total')) {
                $item->line_total = $item->subtotal;
            }
        });
    }

    // ── Relationships ─────────────────────────────────────

    // The order this item belongs to (Order consists of Order_item in ERD).
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    // Alias relationship for backwards compatibility.
    public function invoice()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    // The product referenced by this line item (Product includes Order_item in ERD).
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    // The customer (through order) for convenience.
    public function customer()
    {
        return $this->hasOneThrough(
            Customer::class,
            Order::class,
            'id',           // orders.id
            'id',           // customers.id
            'order_id',     // order_items.order_id
            'customer_id'   // orders.customer_id
        );
    }

    // ── Accessors ─────────────────────────────────────────

    // Profit on this line item.
    public function getLineProfitAttribute(): float
    {
        return ((float) $this->unit_price_snapshot - (float) $this->buying_price_snapshot) * ($this->quantity ?: $this->qty);
    }
}
