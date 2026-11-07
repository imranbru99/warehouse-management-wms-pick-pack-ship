<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Inventory\StockBalanceStatus;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property float $qty
 * @property float $reserved_qty
 * @property Carbon|null $expiry_date
 */
class StockBalance extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'warehouse_id',
        'product_id',
        'location_id',
        'lot_code',
        'serial_code',
        'lpn_id',
        'status',
        'qty',
        'reserved_qty',
        'expiry_date',
    ];

    protected function casts(): array
    {
        return [
            'status' => StockBalanceStatus::class,
            'qty' => 'decimal:3',
            'reserved_qty' => 'decimal:3',
            'expiry_date' => 'date',
        ];
    }

    public function availableQty(): float
    {
        return (float) $this->qty - (float) $this->reserved_qty;
    }

    /** @return BelongsTo<Warehouse, $this> */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
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
