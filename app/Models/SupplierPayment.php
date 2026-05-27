<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierPayment extends Model
{
    use \App\Traits\BelongsToTenant;

    protected $fillable = [
        'supplier_id',
        'description',
        'amount',
        'bill_date',
        'due_date',
        'method',
        'reference',
        'status',
        'paid_on',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'bill_date' => 'date',
            'due_date' => 'date',
            'paid_on' => 'date',
        ];
    }

    // ── Relationships ─────────────────────────────────────

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    // ── Scopes ────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'Paid');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'Pending')
                     ->whereNotNull('due_date')
                     ->where('due_date', '<', now());
    }

    // ── Accessors ─────────────────────────────────────────

    public function getIsPaidAttribute(): bool
    {
        return $this->status === 'Paid';
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->status === 'Pending'
            && $this->due_date !== null
            && $this->due_date < now();
    }
}
