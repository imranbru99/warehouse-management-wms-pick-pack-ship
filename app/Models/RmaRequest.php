<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Returns\RmaRequestStatus;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class RmaRequest extends Model
{
    use BelongsToTenant;
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'warehouse_id',
        'customer_id',
        'sales_order_id',
        'number',
        'status',
        'reason',
        'notes',
        'approved_by',
        'approved_at',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'status' => RmaRequestStatus::class,
            'approved_at' => 'datetime',
            'meta' => 'array',
        ];
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

    /** @return BelongsTo<SalesOrder, $this> */
    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class);
    }

    /** @return BelongsTo<User, $this> */
    public function approvedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /** @return HasMany<RmaLine, $this> */
    public function lines(): HasMany
    {
        return $this->hasMany(RmaLine::class);
    }

    /** @return HasMany<ReturnReceipt, $this> */
    public function returnReceipts(): HasMany
    {
        return $this->hasMany(ReturnReceipt::class);
    }
}
