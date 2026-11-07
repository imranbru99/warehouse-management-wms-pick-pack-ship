<?php

declare(strict_types=1);

use App\Models\Tenant;
use App\Models\Warehouse;
use App\Support\Tenancy\TenantContext;

it('scopes warehouses by tenant context', function () {
    $alpha = Tenant::factory()->create(['name' => 'Alpha Logistics']);
    $beta = Tenant::factory()->create(['name' => 'Beta Logistics']);

    app(TenantContext::class)->set($alpha);
    $warehouseAlpha = Warehouse::factory()->create(['name' => 'Alpha DC', 'code' => 'ALPHA-01']);

    app(TenantContext::class)->set($beta);
    $warehouseBeta = Warehouse::factory()->create(['name' => 'Beta DC', 'code' => 'BETA-01']);

    app(TenantContext::class)->set($alpha);
    $visible = Warehouse::query()->pluck('id')->all();

    expect($visible)->toContain($warehouseAlpha->id)
        ->not->toContain($warehouseBeta->id);

    app(TenantContext::class)->set($beta);
    $visibleBeta = Warehouse::query()->pluck('id')->all();

    expect($visibleBeta)->toContain($warehouseBeta->id)
        ->not->toContain($warehouseAlpha->id);
});
