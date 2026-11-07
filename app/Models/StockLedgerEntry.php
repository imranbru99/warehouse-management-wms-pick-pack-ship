<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Inventory\StockLedgerType;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StockLedgerEntry extends Model
{
    use BelongsToTenant;

    public const UPDATED_AT = null;

    protected $fillable = [
        'tenant_id',
        'warehouse_id',
        'product_id',
        'location_id',
        'lot_code',
        'serial_code',
        'lpn_id',
        'qty_delta',
        'qty_before',
        'qty_after',
        'type',
        'reason',
        'reference_type',
        'reference_id',
        'user_id',
        'device_id',
        'meta',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => StockLedgerType::class,
            'qty_delta' => 'decimal:3',
            'qty_before' => 'decimal:3',
            'qty_after' => 'decimal:3',
            'meta' => 'array',
            'created_at' => 'datetime',
        ];
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

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return MorphTo<Model, $this> */
    public function reference(): MorphTo
    {
        return $this->morphTo();
    }
}
