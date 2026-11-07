<?php

declare(strict_types=1);

namespace App\Filament\App\Pages;

use App\Models\ModuleDefinition;
use App\Support\Modules\ModuleRegistry;
use App\Support\Tenancy\TenantContext;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use RuntimeException;

class ModuleManager extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-squares-plus';

    protected static ?string $navigationLabel = 'Module Manager';

    protected static ?string $title = 'Module Manager';

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 90;

    protected string $view = 'filament.app.pages.module-manager';

    /**
     * @return Collection<int, array{
     *     key: string,
     *     name: string,
     *     description: ?string,
     *     icon: ?string,
     *     features: list<string>,
     *     dependencies: list<string>,
     *     is_core: bool,
     *     enabled: bool,
     *     missing: list<string>,
     *     blocking: list<string>
     * }>
     */
    public function getModulesProperty(): Collection
    {
        $tenantId = app(TenantContext::class)->id();
        $registry = app(ModuleRegistry::class);

        return ModuleDefinition::query()
            ->orderBy('sort_order')
            ->get()
            ->values()
            ->map(function (ModuleDefinition $module) use ($registry, $tenantId): array {
                return [
                    'key' => $module->key,
                    'name' => $module->name,
                    'description' => $module->description,
                    'icon' => $module->icon,
                    'features' => array_values($module->features ?? []),
                    'dependencies' => array_values($module->dependencies ?? []),
                    'is_core' => $module->is_core,
                    'enabled' => $registry->enabled($module->key, $tenantId),
                    'missing' => $registry->missingDependencies($module->key, $tenantId),
                    'blocking' => $registry->blockingDependents($module->key, $tenantId),
                ];
            });
    }

    public function toggle(string $key): void
    {
        $tenantId = app(TenantContext::class)->id();
        if ($tenantId === null) {
            Notification::make()->title('No tenant context')->danger()->send();

            return;
        }

        $registry = app(ModuleRegistry::class);

        try {
            if ($registry->enabled($key, $tenantId)) {
                $registry->disable($key, $tenantId);
                Notification::make()->title("Disabled {$key}")->success()->send();
            } else {
                $missing = $registry->missingDependencies($key, $tenantId);
                if ($missing !== []) {
                    Notification::make()
                        ->title('Missing dependencies')
                        ->body('Enable first: '.implode(', ', $missing))
                        ->warning()
                        ->send();

                    return;
                }
                $registry->enable($key, $tenantId);
                Notification::make()->title("Enabled {$key}")->success()->send();
            }
        } catch (RuntimeException $e) {
            Notification::make()->title('Cannot change module')->body($e->getMessage())->danger()->send();
        }
    }
}
