<?php

declare(strict_types=1);

use App\Enums\Inventory\StockLedgerType;
use App\Exceptions\InsufficientStockException;
use App\Models\Location;
use App\Models\Product;
use App\Models\StockBalance;
use App\Models\Tenant;
use App\Models\Warehouse;
use App\Services\Inventory\StockLedgerService;
use App\Support\Tenancy\TenantContext;

beforeEach(function () {
    $this->tenant = Tenant::factory()->create();
    app(TenantContext::class)->set($this->tenant);

    $this->warehouse = Warehouse::factory()->create(['tenant_id' => $this->tenant->id]);
    $this->product = Product::factory()->create(['tenant_id' => $this->tenant->id]);
    $this->location = Location::query()->create([
        'tenant_id' => $this->tenant->id,
        'warehouse_id' => $this->warehouse->id,
        'code' => 'A-01-01',
        'type' => 'storage',
        'status' => 'active',
    ]);

    $this->ledger = app(StockLedgerService::class);
});

it('increases balance when posting a receive entry', function () {
    $this->ledger->post([
        'warehouse_id' => $this->warehouse->id,
        'product_id' => $this->product->id,
        'location_id' => $this->location->id,
        'qty_delta' => '10',
        'type' => StockLedgerType::Receive,
    ]);

    $balance = StockBalance::query()
        ->where('product_id', $this->product->id)
        ->where('location_id', $this->location->id)
        ->first();

    expect($balance)->not->toBeNull()
        ->and((string) $balance->qty)->toBe('10.000');

    $reconcile = $this->ledger->reconcile($this->product->id, $this->location->id, $this->warehouse->id);
    expect($reconcile['in_sync'])->toBeTrue();
});

it('rejects negative stock by default', function () {
    expect(fn () => $this->ledger->post([
        'warehouse_id' => $this->warehouse->id,
        'product_id' => $this->product->id,
        'location_id' => $this->location->id,
        'qty_delta' => '-1',
        'type' => StockLedgerType::Adjust,
    ]))->toThrow(InsufficientStockException::class);
});

it('scopes stock balances by tenant context', function () {
    $other = Tenant::factory()->create();

    $this->ledger->post([
        'tenant_id' => $this->tenant->id,
        'warehouse_id' => $this->warehouse->id,
        'product_id' => $this->product->id,
        'location_id' => $this->location->id,
        'qty_delta' => '5',
        'type' => StockLedgerType::Receive,
    ]);

    app(TenantContext::class)->set($other);
    expect(StockBalance::query()->count())->toBe(0);

    app(TenantContext::class)->set($this->tenant);
    expect(StockBalance::query()->count())->toBe(1);
});
