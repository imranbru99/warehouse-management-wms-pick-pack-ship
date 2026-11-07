<?php

declare(strict_types=1);

use App\Models\Tenant;
use App\Models\User;
use App\Support\Modules\Module;
use App\Support\Modules\ModuleRegistry;
use App\Support\Tenancy\TenantContext;
use Database\Seeders\ModuleCatalogSeeder;

beforeEach(function () {
    $this->seed(ModuleCatalogSeeder::class);
});

it('treats core module as always enabled', function () {
    $tenant = Tenant::factory()->create();
    app(TenantContext::class)->set($tenant);

    expect(Module::enabled('core'))->toBeTrue();
});

it('enables and disables a module for a tenant', function () {
    $tenant = Tenant::factory()->create();
    app(TenantContext::class)->set($tenant);
    $registry = app(ModuleRegistry::class);

    expect(Module::enabled('warehouse'))->toBeFalse();

    $registry->enable('warehouse', $tenant->id);

    expect(Module::enabled('warehouse'))->toBeTrue();

    $registry->disable('warehouse', $tenant->id);

    expect(Module::enabled('warehouse'))->toBeFalse();
});

it('blocks enabling a module when dependencies are missing', function () {
    $tenant = Tenant::factory()->create();
    $registry = app(ModuleRegistry::class);

    expect(fn () => $registry->enable('inbound', $tenant->id))
        ->toThrow(RuntimeException::class);
});

it('blocks disabling a module when dependents are enabled', function () {
    $tenant = Tenant::factory()->create();
    $registry = app(ModuleRegistry::class);

    $registry->enableWithDependencies('inbound', $tenant->id);

    expect(fn () => $registry->disable('warehouse', $tenant->id))
        ->toThrow(RuntimeException::class);
});

it('returns 403 from module-protected API when disabled', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->create([
        'tenant_id' => $tenant->id,
        'portal_type' => 'staff',
    ]);

    app(TenantContext::class)->set($tenant);

    $this->actingAs($user)
        ->getJson('/api/v1/warehouses')
        ->assertForbidden();
});
