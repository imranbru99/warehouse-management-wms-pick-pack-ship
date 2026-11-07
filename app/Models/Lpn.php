<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lpn extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'warehouse_id',
        'location_id',
        'parent_lpn_id',
        'code',
        'type',
        'status',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
        ];
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

    /** @return BelongsTo<Lpn, $this> */
    public function parentLpn(): BelongsTo
    {
        return $this->belongsTo(Lpn::class, 'parent_lpn_id');
    }

    /** @return HasMany<Lpn, $this> */
    public function childLpns(): HasMany
    {
        return $this->hasMany(Lpn::class, 'parent_lpn_id');
    }
}
