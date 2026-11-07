<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Allocation extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'sales_order_line_id',
        'product_id',
        'location_id',
        'stock_balance_id',
        'lot_code',
        'qty',
        'status',
        'strategy',
    ];

    protected function casts(): array
    {
        return [
            'qty' => 'decimal:3',
        ];
    }

    /** @return BelongsTo<SalesOrderLine, $this> */
    public function salesOrderLine(): BelongsTo
    {
        return $this->belongsTo(SalesOrderLine::class);
    }

    /** @return BelongsTo<Product, $this> */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /** @return BelongsTo<Location, $this> */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /** @return BelongsTo<StockBalance, $this> */
    public function stockBalance(): BelongsTo
    {
        return $this->belongsTo(StockBalance::class);
    }

    /** @return HasOne<PickTask, $this> */
    public function pickTask(): HasOne
    {
        return $this->hasOne(PickTask::class);
    }
}
