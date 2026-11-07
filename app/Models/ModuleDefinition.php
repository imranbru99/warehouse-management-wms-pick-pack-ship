<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property list<string>|null $dependencies
 * @property list<string>|null $permissions
 * @property list<string>|null $features
 * @property array<string, mixed>|null $settings_schema
 */
class ModuleDefinition extends Model
{
    protected $table = 'modules';

    protected $fillable = [
        'key',
        'name',
        'description',
        'icon',
        'version',
        'dependencies',
        'permissions',
        'settings_schema',
        'features',
        'is_core',
        'is_available',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'dependencies' => 'array',
            'permissions' => 'array',
            'settings_schema' => 'array',
            'features' => 'array',
            'is_core' => 'boolean',
            'is_available' => 'boolean',
        ];
    }

    /** @return HasMany<TenantModule, $this> */
    public function tenantModules(): HasMany
    {
        return $this->hasMany(TenantModule::class, 'module_id');
    }
}
