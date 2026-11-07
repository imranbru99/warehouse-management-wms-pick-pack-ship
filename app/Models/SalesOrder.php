<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SalesOrderStatus;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use InvalidArgumentException;

/**
 * @property SalesOrderStatus $status
 * @property array<string, mixed>|null $shipping_address
 * @property array<string, mixed>|null $meta
 */
class SalesOrder extends Model
{
    use BelongsToTenant;
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'warehouse_id',
        'customer_id',
        'number',
        'external_ref',
        'status',
        'priority',
        'ordered_at',
        'required_ship_date',
        'shipping_address',
        'notes',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'status' => SalesOrderStatus::class,
            'priority' => 'integer',
            'ordered_at' => 'datetime',
            'required_ship_date' => 'date',
            'shipping_address' => 'array',
            'meta' => 'array',
        ];
    }

    public function canTransitionTo(SalesOrderStatus $next): bool
    {
        $current = $this->status;

        if ($next === SalesOrderStatus::Cancelled) {
            return $current !== SalesOrderStatus::Shipped && $current !== SalesOrderStatus::Cancelled;
        }

        $order = SalesOrderStatus::ordered();
        $currentIndex = array_search($current, $order, true);
        $nextIndex = array_search($next, $order, true);

        if ($currentIndex === false || $nextIndex === false) {
            return false;
        }

        return $nextIndex === $currentIndex + 1;
    }

    public function transitionTo(SalesOrderStatus $next): void
    {
        if (! $this->canTransitionTo($next)) {
            throw new InvalidArgumentException("Cannot transition order from {$this->status->value} to {$next->value}");
        }

        $this->status = $next;
        $this->save();
    }

    public function confirm(): void
    {
        $this->transitionTo(SalesOrderStatus::Confirmed);
    }

    /** @return BelongsTo<Warehouse, $this> */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /** @return BelongsTo<Customer, $this> */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /** @return HasMany<SalesOrderLine, $this> */
    public function lines(): HasMany
    {
        return $this->hasMany(SalesOrderLine::class);
    }

    /** @return HasMany<Shipment, $this> */
    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }

    /** @return HasMany<PickTask, $this> */
    public function pickTasks(): HasMany
    {
        return $this->hasMany(PickTask::class);
    }
}
