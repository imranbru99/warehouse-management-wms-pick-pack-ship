<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Database\Factories\WarehouseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use ImranDev\UniversalSlug\SlugOptions;
use ImranDev\UniversalSlug\Traits\HasUniversalSlug;

class Warehouse extends Model
{
    use BelongsToTenant;

    /** @use HasFactory<WarehouseFactory> */
    use HasFactory;

    use HasUniversalSlug;
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'code',
        'name',
        'slug',
        'timezone',
        'status',
        'address',
        'settings',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'address' => 'array',
            'settings' => 'array',
            'is_default' => 'boolean',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->skipIfFilled()
            ->uniqueScope(fn ($query, $model) => $query->where('tenant_id', $model->tenant_id));
    }

    /** @return HasMany<Zone, $this> */
    public function zones(): HasMany
    {
        return $this->hasMany(Zone::class);
    }

    /** @return HasMany<Location, $this> */
    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }

    /** @return HasMany<Lpn, $this> */
    public function lpns(): HasMany
    {
        return $this->hasMany(Lpn::class);
    }
}
