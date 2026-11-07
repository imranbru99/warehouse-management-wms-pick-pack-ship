<?php

declare(strict_types=1);

namespace App\Support\Modules;

use App\Models\ModuleDefinition;
use App\Models\TenantModule;
use App\Support\Tenancy\TenantContext;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use RuntimeException;

class ModuleRegistry
{
    /**
     * @return Collection<int, ModuleDefinition>
     */
    public function catalog(): Collection
    {
        $cached = Cache::get('wms.modules.catalog');

        if ($cached instanceof Collection) {
            return $cached;
        }

        if ($cached !== null) {
            Cache::forget('wms.modules.catalog');
        }

        /** @var Collection<int, ModuleDefinition> $catalog */
        $catalog = ModuleDefinition::query()->orderBy('sort_order')->get();
        Cache::put('wms.modules.catalog', $catalog, 3600);

        return $catalog;
    }

    public function forgetCatalogCache(): void
    {
        Cache::forget('wms.modules.catalog');
    }

    public function enabled(string $key, ?int $tenantId = null): bool
    {
        $definition = $this->catalog()->firstWhere('key', $key);

        if ($definition === null) {
            return false;
        }

        if ($definition->is_core) {
            return true;
        }

        $tenantId ??= app(TenantContext::class)->id();

        if ($tenantId === null) {
            return false;
        }

        return (bool) Cache::remember(
            "wms.tenant.{$tenantId}.module.{$key}",
            600,
            fn () => TenantModule::query()
                ->where('tenant_id', $tenantId)
                ->where('module_key', $key)
                ->where('is_enabled', true)
                ->exists()
        );
    }

    public function forgetTenantCache(int $tenantId, ?string $key = null): void
    {
        if ($key !== null) {
            Cache::forget("wms.tenant.{$tenantId}.module.{$key}");

            return;
        }

        foreach ($this->catalog() as $module) {
            Cache::forget("wms.tenant.{$tenantId}.module.{$module->key}");
        }
    }

    /**
     * @return list<string>
     */
    public function missingDependencies(string $key, ?int $tenantId = null): array
    {
        $definition = $this->catalog()->firstWhere('key', $key);
        if ($definition === null) {
            return [];
        }

        $missing = [];
        $dependencies = $definition->dependencies ?? [];
        if ($dependencies === []) {
            return $missing;
        }

        foreach ($dependencies as $dependency) {
            if (! $this->enabled($dependency, $tenantId)) {
                $missing[] = $dependency;
            }
        }

        return $missing;
    }

    /**
     * @return list<string>
     */
    public function dependents(string $key): array
    {
        return $this->catalog()
            ->filter(fn (ModuleDefinition $m) => in_array($key, $m->dependencies ?? [], true))
            ->pluck('key')
            ->all();
    }

    /**
     * @return list<string>
     */
    public function blockingDependents(string $key, ?int $tenantId = null): array
    {
        $blocking = [];
        foreach ($this->dependents($key) as $dependent) {
            if ($this->enabled($dependent, $tenantId)) {
                $blocking[] = $dependent;
            }
        }

        return $blocking;
    }

    /**
     * @param  array<string, mixed>  $settings
     */
    public function enable(string $key, int $tenantId, array $settings = []): TenantModule
    {
        $definition = ModuleDefinition::query()->where('key', $key)->firstOrFail();

        if (! $definition->is_available && ! $definition->is_core) {
            throw new RuntimeException("Module [{$key}] is not available on this plan.");
        }

        $missing = $this->missingDependencies($key, $tenantId);
        if ($missing !== []) {
            throw new RuntimeException(
                'Enable required modules first: '.implode(', ', $missing)
            );
        }

        $record = TenantModule::query()->updateOrCreate(
            ['tenant_id' => $tenantId, 'module_key' => $key],
            [
                'module_id' => $definition->id,
                'is_enabled' => true,
                'settings' => $settings,
                'enabled_at' => now(),
                'disabled_at' => null,
            ]
        );

        $this->forgetTenantCache($tenantId, $key);

        return $record;
    }

    public function disable(string $key, int $tenantId): TenantModule
    {
        $definition = ModuleDefinition::query()->where('key', $key)->firstOrFail();

        if ($definition->is_core) {
            throw new RuntimeException('Core module cannot be disabled.');
        }

        $blocking = $this->blockingDependents($key, $tenantId);
        if ($blocking !== []) {
            throw new RuntimeException(
                "Cannot disable [{$key}] while dependent modules are enabled: ".implode(', ', $blocking)
            );
        }

        $record = TenantModule::query()->updateOrCreate(
            ['tenant_id' => $tenantId, 'module_key' => $key],
            [
                'module_id' => $definition->id,
                'is_enabled' => false,
                'disabled_at' => now(),
            ]
        );

        $this->forgetTenantCache($tenantId, $key);

        return $record;
    }

    /**
     * Apply a business preset: enable listed modules, disable the rest (except core).
     *
     * @param  list<string>  $keys
     */
    public function applyPreset(int $tenantId, array $keys): void
    {
        foreach ($this->catalog() as $module) {
            if ($module->is_core) {
                $this->enable($module->key, $tenantId);

                continue;
            }

            if (in_array($module->key, $keys, true)) {
                // Enable dependencies first in topological-ish order by retrying
                $this->enableWithDependencies($module->key, $tenantId);
            }
        }

        // Disable modules not in preset (reverse dependency order)
        $toDisable = $this->catalog()
            ->reject(fn (ModuleDefinition $m) => $m->is_core || in_array($m->key, $keys, true))
            ->sortByDesc('sort_order');

        foreach ($toDisable as $module) {
            try {
                $this->disable($module->key, $tenantId);
            } catch (RuntimeException) {
                // Skip if still blocked; another pass may clear
            }
        }
    }

    public function enableWithDependencies(string $key, int $tenantId): void
    {
        $definition = $this->catalog()->firstWhere('key', $key);
        if ($definition === null) {
            return;
        }

        $dependencies = $definition->dependencies ?? [];
        if ($dependencies !== []) {
            foreach ($dependencies as $dependency) {
                $this->enableWithDependencies($dependency, $tenantId);
            }
        }

        if (! $this->enabled($key, $tenantId)) {
            $this->enable($key, $tenantId);
        }
    }
}
