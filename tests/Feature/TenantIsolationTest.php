<?php

declare(strict_types=1);

use App\Models\ModuleDefinition;
use App\Models\Tenant;
use App\Models\TenantModule;
use App\Models\User;
use App\Support\Modules\Module;
use App\Support\Modules\ModuleRegistry;
use App\Support\Tenancy\TenantContext;
use Database\Seeders\ModuleCatalogSeeder;

beforeEach(function () {
    $this->seed(ModuleCatalogSeeder::class);
});

it('scopes tenant modules by tenant context', function () {
    $a = Tenant::factory()->create(['name' => 'Alpha Co']);
    $b = Tenant::factory()->create(['name' => 'Beta Co']);

    $moduleId = ModuleDefinition::query()->where('key', 'warehouse')->value('id');

    TenantModule::query()->create([
        'tenant_id' => $a->id,
        'module_id' => $moduleId,
        'module_key' => 'warehouse',
        'is_enabled' => true,
        'enabled_at' => now(),
    ]);

    TenantModule::query()->create([
        'tenant_id' => $b->id,
        'module_id' => $moduleId,
        'module_key' => 'warehouse',
        'is_enabled' => false,
    ]);

    app(TenantContext::class)->set($a);
    expect(Module::enabled('warehouse'))->toBeTrue();

    app(TenantContext::class)->set($b);
    app(ModuleRegistry::class)->forgetTenantCache($b->id, 'warehouse');
    expect(Module::enabled('warehouse'))->toBeFalse();
});

it('isolates users by tenant_id', function () {
    $a = Tenant::factory()->create();
    $b = Tenant::factory()->create();

    $userA = User::factory()->create(['tenant_id' => $a->id, 'email' => 'a@example.com']);
    $userB = User::factory()->create(['tenant_id' => $b->id, 'email' => 'b@example.com']);

    expect($userA->tenant_id)->not->toBe($userB->tenant_id)
        ->and(User::query()->where('tenant_id', $a->id)->pluck('id')->all())
        ->toContain($userA->id)
        ->not->toContain($userB->id);
});
