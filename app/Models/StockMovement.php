<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = [
        'date',
        'sales_person',
        'product_id',
        'issued_qty',
        'returned_qty',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'issued_qty' => 'integer',
            'returned_qty' => 'integer',
        ];
    }

    // ── Relationships ─────────────────────────────────────

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // ── Accessors ─────────────────────────────────────────

    public function getSoldQtyAttribute(): int
    {
        return $this->issued_qty - $this->returned_qty;
    }
}
