<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use \App\Traits\BelongsToTenant;

    protected $fillable = [
        'supplier_code',
        'name',
        'contact_person',
        'phone',
        'category',
        'terms_days',
        'payment_method',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'terms_days' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    // ── Relationships ─────────────────────────────────────

    // All payment records for this supplier.
    public function payments()
    {
        return $this->hasMany(SupplierPayment::class);
    }

    // Only pending (unpaid) payments.
    public function pendingPayments()
    {
        return $this->hasMany(SupplierPayment::class)->where('status', 'Pending');
    }

    // Only paid payments.
    public function paidPayments()
    {
        return $this->hasMany(SupplierPayment::class)->where('status', 'Paid');
    }

    // ── Scopes ────────────────────────────────────────────

    // Scope: only active suppliers.
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ── Accessors ─────────────────────────────────────────

    // Total amount owed to this supplier (all payments).
    public function getTotalOwedAttribute(): float
    {
        return (float) $this->payments()->sum('amount');
    }

    // Total amount already paid to this supplier.
    public function getTotalPaidAttribute(): float
    {
        return (float) $this->payments()->where('status', 'Paid')->sum('amount');
    }

    // Outstanding balance to this supplier.
    public function getBalanceAttribute(): float
    {
        return (float) $this->payments()->where('status', 'Pending')->sum('amount');
    }

    // Count of pending payments.
    public function getPendingCountAttribute(): int
    {
        return (int) $this->payments()->where('status', 'Pending')->count();
    }
}
