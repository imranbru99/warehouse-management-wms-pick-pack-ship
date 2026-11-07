<?php

declare(strict_types=1);

use App\Models\ModuleDefinition;
use App\Models\Product;
use App\Models\Tenant;
use App\Models\TenantModule;
use App\Models\User;
use App\Models\Warehouse;
use App\Support\Tenancy\TenantContext;
use Database\Seeders\ModuleCatalogSeeder;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->seed(ModuleCatalogSeeder::class);
});

it('lists and creates sales orders via API when outbound module is enabled', function () {
    $tenant = Tenant::factory()->create();
    app(TenantContext::class)->set($tenant);

    $moduleId = ModuleDefinition::query()->where('key', 'outbound')->value('id');
    TenantModule::query()->create([
        'tenant_id' => $tenant->id,
        'module_id' => $moduleId,
        'module_key' => 'outbound',
        'is_enabled' => true,
        'enabled_at' => now(),
    ]);

    $user = User::factory()->create(['tenant_id' => $tenant->id]);
    Sanctum::actingAs($user);

    $warehouse = Warehouse::factory()->create();
    $product = Product::factory()->create();

    $create = $this->postJson('/api/v1/sales-orders', [
        'warehouse_id' => $warehouse->id,
        'lines' => [
            ['product_id' => $product->id, 'qty_ordered' => 2],
        ],
    ], ['X-Tenant' => $tenant->slug]);

    $create->assertCreated()
        ->assertJsonPath('number', fn ($n) => is_string($n) && $n !== '');

    $list = $this->getJson('/api/v1/sales-orders', ['X-Tenant' => $tenant->slug]);
    $list->assertOk()
        ->assertJsonPath('data.0.id', $create->json('id'));
});
