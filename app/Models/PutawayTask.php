<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Inbound\PutawayTaskStatus;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property PutawayTaskStatus $status
 * @property Carbon|null $completed_at
 */
class PutawayTask extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'warehouse_id',
        'warehouse_task_id',
        'receipt_id',
        'receipt_line_id',
        'product_id',
        'from_location_id',
        'to_location_id',
        'qty',
        'lot_code',
        'serial_code',
        'lpn_id',
        'status',
        'priority',
        'assigned_to',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => PutawayTaskStatus::class,
            'qty' => 'decimal:3',
            'priority' => 'integer',
            'completed_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<Warehouse, $this> */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /** @return BelongsTo<WarehouseTask, $this> */
    public function warehouseTask(): BelongsTo
    {
        return $this->belongsTo(WarehouseTask::class);
    }

    /** @return BelongsTo<Receipt, $this> */
    public function receipt(): BelongsTo
    {
        return $this->belongsTo(Receipt::class);
    }

    /** @return BelongsTo<ReceiptLine, $this> */
    public function receiptLine(): BelongsTo
    {
        return $this->belongsTo(ReceiptLine::class);
    }

    /** @return BelongsTo<Product, $this> */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /** @return BelongsTo<Location, $this> */
    public function fromLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'from_location_id');
    }

    /** @return BelongsTo<Location, $this> */
    public function toLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'to_location_id');
    }

    /** @return BelongsTo<Lpn, $this> */
    public function lpn(): BelongsTo
    {
        return $this->belongsTo(Lpn::class);
    }

    /** @return BelongsTo<User, $this> */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
