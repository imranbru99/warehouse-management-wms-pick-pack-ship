<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Returns\ReturnReceiptStatus;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnReceipt extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'rma_request_id',
        'warehouse_id',
        'location_id',
        'number',
        'status',
        'received_by',
        'received_at',
        'notes',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'status' => ReturnReceiptStatus::class,
            'received_at' => 'datetime',
            'meta' => 'array',
        ];
    }

    /** @return BelongsTo<RmaRequest, $this> */
    public function rmaRequest(): BelongsTo
    {
        return $this->belongsTo(RmaRequest::class);
    }

    /** @return BelongsTo<Warehouse, $this> */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
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
}
