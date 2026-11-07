<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PickTask extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'wave_id',
        'allocation_id',
        'sales_order_id',
        'location_id',
        'product_id',
        'qty',
        'pick_sequence',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'qty' => 'decimal:3',
            'pick_sequence' => 'integer',
        ];
    }

    /** @return BelongsTo<Wave, $this> */
    public function wave(): BelongsTo
    {
        return $this->belongsTo(Wave::class);
    }

    /** @return BelongsTo<Allocation, $this> */
    public function allocation(): BelongsTo
    {
        return $this->belongsTo(Allocation::class);
    }

    /** @return BelongsTo<SalesOrder, $this> */
    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class);
    }

    /** @return BelongsTo<Location, $this> */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /** @return BelongsTo<Product, $this> */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
