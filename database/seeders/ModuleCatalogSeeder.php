<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\ModuleDefinition;
use App\Support\Modules\ModuleRegistry;
use Illuminate\Database\Seeder;

class ModuleCatalogSeeder extends Seeder
{
    public function run(): void
    {
        foreach (glob(base_path('modules/*/module.json')) ?: [] as $file) {
            $data = json_decode((string) file_get_contents($file), true, 512, JSON_THROW_ON_ERROR);

            ModuleDefinition::query()->updateOrCreate(
                ['key' => $data['key']],
                [
                    'name' => $data['name'],
                    'description' => $data['description'] ?? null,
                    'icon' => $data['icon'] ?? null,
                    'version' => $data['version'] ?? '1.0.0',
                    'dependencies' => $data['dependencies'] ?? [],
                    'permissions' => $data['permissions'] ?? [],
                    'settings_schema' => $data['settings_schema'] ?? [],
                    'features' => $data['features'] ?? [],
                    'is_core' => (bool) ($data['is_core'] ?? false),
                    'is_available' => true,
                    'sort_order' => (int) ($data['sort_order'] ?? 0),
                ]
            );
        }

        app(ModuleRegistry::class)->forgetCatalogCache();
    }
}
