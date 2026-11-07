<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ShipmentStatus;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use InvalidArgumentException;

/**
 * @property ShipmentStatus $status
 * @property Carbon|null $shipped_at
 * @property array<string, mixed>|null $ship_to
 * @property array<string, mixed>|null $meta
 */
class Shipment extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'warehouse_id',
        'sales_order_id',
        'number',
        'tracking_number',
        'carrier',
        'service',
        'status',
        'shipped_at',
        'ship_to',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'status' => ShipmentStatus::class,
            'shipped_at' => 'datetime',
            'ship_to' => 'array',
            'meta' => 'array',
        ];
    }

    private const TRANSITIONS = [
        'draft' => ['packed', 'cancelled'],
        'packed' => ['label_created', 'cancelled'],
        'label_created' => ['shipped', 'cancelled'],
        'shipped' => ['in_transit'],
        'in_transit' => ['delivered'],
    ];

    public function canTransitionTo(ShipmentStatus $next): bool
    {
        $allowed = self::TRANSITIONS[$this->status->value] ?? [];

        return in_array($next->value, $allowed, true) || $next === ShipmentStatus::Cancelled;
    }

    public function transitionTo(ShipmentStatus $next): void
    {
        if (! $this->canTransitionTo($next)) {
            throw new InvalidArgumentException("Cannot transition shipment from {$this->status->value} to {$next->value}");
        }

        $this->status = $next;
        if ($next === ShipmentStatus::Shipped && $this->shipped_at === null) {
            $this->shipped_at = now();
        }
        $this->save();
    }

    /** @return BelongsTo<Warehouse, $this> */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /** @return BelongsTo<SalesOrder, $this> */
    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class);
    }

    /** @return HasMany<Carton, $this> */
    public function cartons(): HasMany
    {
        return $this->hasMany(Carton::class);
    }

    /** @return HasMany<ShipmentPackage, $this> */
    public function packages(): HasMany
    {
        return $this->hasMany(ShipmentPackage::class);
    }

    /** @return HasMany<TrackingEvent, $this> */
    public function trackingEvents(): HasMany
    {
        return $this->hasMany(TrackingEvent::class)->orderBy('occurred_at');
    }
}
