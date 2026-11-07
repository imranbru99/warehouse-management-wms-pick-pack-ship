<?php

declare(strict_types=1);

namespace App\Filament\Concerns;

use App\Support\Modules\Module;

trait HidesWhenModuleDisabled
{
    public static function getModuleKey(): ?string
    {
        return property_exists(static::class, 'moduleKey')
            ? static::$moduleKey
            : null;
    }

    public static function shouldRegisterNavigation(): bool
    {
        $key = static::getModuleKey();

        if ($key === null || $key === 'core') {
            return true;
        }

        return Module::enabled($key);
    }

    public static function canAccess(): bool
    {
        $key = static::getModuleKey();

        if ($key === null || $key === 'core') {
            return true;
        }

        return Module::enabled($key);
    }
}
