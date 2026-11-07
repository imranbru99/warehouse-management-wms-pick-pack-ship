<?php

declare(strict_types=1);

use App\Support\Modules\ModuleRegistry;
use App\Support\Tenancy\TenantContext;

if (! function_exists('tenant')) {
    function tenant(?string $attribute = null): mixed
    {
        $tenant = app(TenantContext::class)->tenant();

        if ($attribute === null) {
            return $tenant;
        }

        return $tenant?->getAttribute($attribute);
    }
}

if (! function_exists('tenant_id')) {
    function tenant_id(): ?int
    {
        return app(TenantContext::class)->id();
    }
}

if (! function_exists('module_enabled')) {
    function module_enabled(string $key): bool
    {
        return app(ModuleRegistry::class)->enabled($key);
    }
}
