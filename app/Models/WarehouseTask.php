<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Inventory\WarehouseTaskState;
use App\Enums\Inventory\WarehouseTaskType;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class WarehouseTask extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'warehouse_id',
        'type',
        'state',
        'priority',
        'sla_due_at',
        'assigned_to',
        'reference_type',
        'reference_id',
        'payload',
        'started_at',
        'completed_at',
        'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => WarehouseTaskType::class,
            'state' => WarehouseTaskState::class,
            'priority' => 'integer',
            'sla_due_at' => 'datetime',
            'payload' => 'array',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<Warehouse, $this> */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /** @return BelongsTo<User, $this> */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /** @return MorphTo<Model, $this> */
    public function reference(): MorphTo
    {
        return $this->morphTo();
    }
}
