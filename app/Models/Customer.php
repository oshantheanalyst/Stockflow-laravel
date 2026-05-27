<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use \App\Traits\BelongsToTenant;

    protected $fillable = [
        'customer_code',
        'name',
        'phone',
        'area',
        'address',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // ── Relationships ─────────────────────────────────────

    // All invoices belonging to this customer.
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    // All invoice items across all invoices for this customer.
    public function invoiceItems()
    {
        return $this->hasManyThrough(InvoiceItem::class, Invoice::class);
    }

    // All invoice returns across all invoices for this customer.
    public function invoiceReturns()
    {
        return $this->hasManyThrough(InvoiceReturn::class, Invoice::class);
    }

    // ── Scopes ────────────────────────────────────────────

    // Scope: only active customers.
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ── Accessors ─────────────────────────────────────────

    // Compute total bought from non-deleted invoices.
    public function getTotalBoughtAttribute(): float
    {
        return (float) $this->invoices()->where('is_deleted', false)->sum('total');
    }

    // Compute total outstanding balance (unpaid invoices).
    public function getTotalOutstandingAttribute(): float
    {
        return (float) $this->invoices()
            ->where('is_deleted', false)
            ->where('is_paid', false)
            ->sum('total');
    }

    // Count of active (non-deleted) invoices.
    public function getInvoiceCountAttribute(): int
    {
        return (int) $this->invoices()->where('is_deleted', false)->count();
    }
}
