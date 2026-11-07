<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use ImranDev\UniversalSlug\SlugOptions;
use ImranDev\UniversalSlug\Traits\HasUniversalSlug;

class Product extends Model
{
    use BelongsToTenant;

    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    use HasUniversalSlug;
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'sku',
        'name',
        'slug',
        'status',
        'description',
        'brand',
        'category',
        'abc_class',
        'track_lot',
        'track_serial',
        'track_expiry',
        'shelf_life_strategy',
        'weight',
        'length',
        'width',
        'height',
        'hs_code',
        'country_of_origin',
        'reorder_point',
        'reorder_qty',
        'min_qty',
        'max_qty',
        'attributes',
    ];

    protected function casts(): array
    {
        return [
            'track_lot' => 'boolean',
            'track_serial' => 'boolean',
            'track_expiry' => 'boolean',
            'weight' => 'decimal:3',
            'length' => 'decimal:3',
            'width' => 'decimal:3',
            'height' => 'decimal:3',
            'reorder_point' => 'decimal:3',
            'reorder_qty' => 'decimal:3',
            'min_qty' => 'decimal:3',
            'max_qty' => 'decimal:3',
            'attributes' => 'array',
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

    /** @return HasMany<ProductBarcode, $this> */
    public function barcodes(): HasMany
    {
        return $this->hasMany(ProductBarcode::class);
    }

    /** @return HasMany<Uom, $this> */
    public function uoms(): HasMany
    {
        return $this->hasMany(Uom::class);
    }
}
