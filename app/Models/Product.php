<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use \App\Traits\BelongsToTenant;

    protected $fillable = [
        'product_code',
        'name',
        'category_id',
        'category',
        'unit',
        'buying_price',
        'selling_price',
        'current_stock',
        'reorder_level',
        'is_manufactured',
        'is_active',
        'price',
        'stock_level',
        'photo_path',
    ];

    protected function casts(): array
    {
        return [
            'buying_price' => 'decimal:2',
            'selling_price' => 'decimal:2',
            'current_stock' => 'decimal:2',
            'reorder_level' => 'decimal:2',
            'is_manufactured' => 'boolean',
            'is_active' => 'boolean',
            'price' => 'decimal:2',
            'stock_level' => 'decimal:2',
        ];
    }

    protected static function booted()
    {
        static::saving(function ($product) {
            // Sync with Category table
            if ($product->isDirty('category') || empty($product->category_id)) {
                $categoryName = trim($product->category ?: 'General');
                if ($categoryName !== '') {
                    $cat = Category::firstOrCreate(['category_name' => $categoryName]);
                    $product->category_id = $cat->id;
                }
            }

            if ($product->isDirty('selling_price')) {
                $product->price = $product->selling_price;
            } elseif ($product->isDirty('price')) {
                $product->selling_price = $product->price;
            }
            
            if ($product->isDirty('current_stock')) {
                $product->stock_level = $product->current_stock;
            } elseif ($product->isDirty('stock_level')) {
                $product->current_stock = $product->stock_level;
            }
        });
    }

    public function categoryRelation()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    // ── Accessors ─────────────────────────────────────────

    // Full public URL to the product photo, or null if none uploaded.
    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo_path ? asset('storage/' . $this->photo_path) : null;
    }

    // ── Relationships ─────────────────────────────────────

    // Invoice line-items that reference this product.
    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    // Invoices that contain this product (many-to-many through invoice_items).
    public function invoices()
    {
        return $this->belongsToMany(Invoice::class, 'invoice_items')
                    ->withPivot('qty', 'unit_price_snapshot', 'buying_price_snapshot', 'line_total')
                    ->withTimestamps();
    }



    // Stock movement records for this product.
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    // ── Scopes ────────────────────────────────────────────

    // Scope: only active products.
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope: only manufactured products.
    public function scopeManufactured($query)
    {
        return $query->where('is_manufactured', true);
    }

    // Scope: products at or below reorder level.
    public function scopeLowStock($query)
    {
        return $query->whereColumn('current_stock', '<=', 'reorder_level');
    }

    // ── Accessors ─────────────────────────────────────────

    // Profit margin per unit.
    public function getProfitMarginAttribute(): float
    {
        return (float) $this->selling_price - (float) $this->buying_price;
    }

    // Whether stock is at or below the reorder level.
    public function getIsLowStockAttribute(): bool
    {
        return (float) $this->current_stock <= (float) $this->reorder_level;
    }

    // Get the order items associated with this product.
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'product_id');
    }
}
