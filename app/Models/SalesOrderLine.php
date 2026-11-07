<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesOrderLine extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'sales_order_id',
        'product_id',
        'line_number',
        'sku',
        'qty_ordered',
        'qty_allocated',
        'qty_picked',
        'qty_shipped',
        'unit_price',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'qty_ordered' => 'decimal:3',
            'qty_allocated' => 'decimal:3',
            'qty_picked' => 'decimal:3',
            'qty_shipped' => 'decimal:3',
            'unit_price' => 'decimal:4',
            'line_number' => 'integer',
        ];
    }

    public function qtyRemainingToAllocate(): float
    {
        return max(0, (float) $this->qty_ordered - (float) $this->qty_allocated);
    }

    /** @return BelongsTo<SalesOrder, $this> */
    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class);
    }

    /** @return BelongsTo<Product, $this> */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /** @return HasMany<Allocation, $this> */
    public function allocations(): HasMany
    {
        return $this->hasMany(Allocation::class);
    }
}
