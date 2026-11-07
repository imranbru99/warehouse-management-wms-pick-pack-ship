<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\TenantFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use ImranDev\UniversalSlug\SlugOptions;
use ImranDev\UniversalSlug\Traits\HasUniversalSlug;

class Tenant extends Model
{
    /** @use HasFactory<TenantFactory> */
    use HasFactory;

    use HasUniversalSlug;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'subdomain',
        'custom_domain',
        'preset',
        'locale',
        'timezone',
        'currency',
        'unit_system',
        'document_locale',
        'status',
        'is_demo',
        'trial_ends_at',
        'settings',
    ];

    protected function casts(): array
    {
        return [
            'settings' => 'array',
            'is_demo' => 'boolean',
            'trial_ends_at' => 'datetime',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->skipIfFilled()
            ->doNotGenerateSlugsOnUpdate();
    }

    /** @return HasMany<User, $this> */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /** @return HasMany<TenantModule, $this> */
    public function tenantModules(): HasMany
    {
        return $this->hasMany(TenantModule::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
