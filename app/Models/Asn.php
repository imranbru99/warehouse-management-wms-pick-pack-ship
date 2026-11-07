<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Inbound\AsnStatus;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asn extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'warehouse_id',
        'supplier_id',
        'purchase_order_id',
        'number',
        'status',
        'expected_at',
        'arrived_at',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'status' => AsnStatus::class,
            'expected_at' => 'datetime',
            'arrived_at' => 'datetime',
            'meta' => 'array',
        ];
    }

    /** @return BelongsTo<Warehouse, $this> */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /** @return BelongsTo<Supplier, $this> */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /** @return BelongsTo<PurchaseOrder, $this> */
    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    /** @return HasMany<AsnLine, $this> */
    public function lines(): HasMany
    {
        return $this->hasMany(AsnLine::class);
    }
}
