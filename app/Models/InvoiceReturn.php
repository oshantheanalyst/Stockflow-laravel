<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceReturn extends Model
{
    protected $fillable = [
        'invoice_id',
        'return_date',
        'total_amount',
        'reason',
    ];

    protected function casts(): array
    {
        return [
            'return_date' => 'date',
            'total_amount' => 'decimal:2',
        ];
    }

    // ── Relationships ─────────────────────────────────────

    // The invoice this return is filed against.
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    // Line items in this return.
    public function items()
    {
        return $this->hasMany(InvoiceReturnItem::class);
    }

    // The customer (via the parent invoice).
    public function customer()
    {
        return $this->hasOneThrough(
            Customer::class,
            Invoice::class,
            'id',           // invoices.id
            'id',           // customers.id
            'invoice_id',   // invoice_returns.invoice_id
            'customer_id'   // invoices.customer_id
        );
    }

    // ── Accessors ─────────────────────────────────────────

    // Number of distinct line items in this return.
    public function getItemCountAttribute(): int
    {
        return $this->items()->count();
    }
}
