<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Inbound\ReceiptStatus;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Receipt extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'warehouse_id',
        'purchase_order_id',
        'asn_id',
        'supplier_id',
        'number',
        'status',
        'location_id',
        'received_by',
        'received_at',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'status' => ReceiptStatus::class,
            'received_at' => 'datetime',
            'meta' => 'array',
        ];
    }

    /** @return BelongsTo<Warehouse, $this> */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /** @return BelongsTo<PurchaseOrder, $this> */
    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    /** @return BelongsTo<Asn, $this> */
    public function asn(): BelongsTo
    {
        return $this->belongsTo(Asn::class);
    }

    /** @return BelongsTo<Supplier, $this> */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /** @return BelongsTo<Location, $this> */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /** @return BelongsTo<User, $this> */
    public function receivedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    /** @return HasMany<ReceiptLine, $this> */
    public function lines(): HasMany
    {
        return $this->hasMany(ReceiptLine::class);
    }

    /** @return HasMany<PutawayTask, $this> */
    public function putawayTasks(): HasMany
    {
        return $this->hasMany(PutawayTask::class);
    }
}
