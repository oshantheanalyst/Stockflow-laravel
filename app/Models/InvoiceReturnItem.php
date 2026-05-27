<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceReturnItem extends Model
{
    protected $fillable = [
        'invoice_return_id',
        'product_id',
        'qty_returned',
        'unit_price',
        'line_total',
    ];

    protected function casts(): array
    {
        return [
            'qty_returned' => 'decimal:2',
            'unit_price' => 'decimal:2',
            'line_total' => 'decimal:2',
        ];
    }

    // ── Relationships ─────────────────────────────────────

    // The return record this item belongs to.
    public function invoiceReturn()
    {
        return $this->belongsTo(InvoiceReturn::class);
    }

    // The product being returned.
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // The parent invoice (through the return).
    public function invoice()
    {
        return $this->hasOneThrough(
            Invoice::class,
            InvoiceReturn::class,
            'id',                 // invoice_returns.id
            'id',                 // invoices.id
            'invoice_return_id',  // invoice_return_items.invoice_return_id
            'invoice_id'          // invoice_returns.invoice_id
        );
    }
}
