<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Inventory\StockBalanceStatus;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockAdjustmentLine extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'stock_adjustment_id',
        'product_id',
        'location_id',
        'qty_delta',
        'lot_code',
        'serial_code',
        'lpn_id',
        'balance_status',
        'line_number',
    ];

    protected function casts(): array
    {
        return [
            'qty_delta' => 'decimal:3',
            'balance_status' => StockBalanceStatus::class,
            'line_number' => 'integer',
        ];
    }

    /** @return BelongsTo<StockAdjustment, $this> */
    public function stockAdjustment(): BelongsTo
    {
        return $this->belongsTo(StockAdjustment::class);
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

    /** @return BelongsTo<Lpn, $this> */
    public function lpn(): BelongsTo
    {
        return $this->belongsTo(Lpn::class);
    }
}
