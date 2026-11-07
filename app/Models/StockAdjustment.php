<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Inventory\StockAdjustmentStatus;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockAdjustment extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'warehouse_id',
        'number',
        'status',
        'reason',
        'created_by',
        'posted_by',
        'posted_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => StockAdjustmentStatus::class,
            'posted_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<Warehouse, $this> */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /** @return BelongsTo<User, $this> */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** @return BelongsTo<User, $this> */
    public function poster(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    /** @return HasMany<StockAdjustmentLine, $this> */
    public function lines(): HasMany
    {
        return $this->hasMany(StockAdjustmentLine::class);
    }
}
