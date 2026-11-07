<?php

declare(strict_types=1);

namespace App\Support\Modules;

/**
 * Static facade-style helpers for module checks.
 */
final class Module
{
    public static function enabled(string $key, ?int $tenantId = null): bool
    {
        return app(ModuleRegistry::class)->enabled($key, $tenantId);
    }

    public static function registry(): ModuleRegistry
    {
        return app(ModuleRegistry::class);
    }
}
