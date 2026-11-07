<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    use BelongsToTenant;
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'warehouse_id',
        'zone_id',
        'code',
        'barcode',
        'type',
        'aisle',
        'rack',
        'level',
        'bin',
        'pick_sequence',
        'max_weight',
        'max_volume',
        'max_pallets',
        'max_units',
        'is_pick_face',
        'is_blocked',
        'status',
        'restrictions',
        'dimensions',
    ];

    protected function casts(): array
    {
        return [
            'pick_sequence' => 'integer',
            'max_weight' => 'decimal:3',
            'max_volume' => 'decimal:3',
            'max_pallets' => 'integer',
            'max_units' => 'integer',
            'is_pick_face' => 'boolean',
            'is_blocked' => 'boolean',
            'restrictions' => 'array',
            'dimensions' => 'array',
        ];
    }

    /** @return BelongsTo<Warehouse, $this> */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /** @return BelongsTo<Zone, $this> */
    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    /** @return HasMany<Lpn, $this> */
    public function lpns(): HasMany
    {
        return $this->hasMany(Lpn::class);
    }
}
