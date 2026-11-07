<?php

declare(strict_types=1);

use App\Enums\Inventory\StockBalanceStatus;
use App\Enums\SalesOrderStatus;
use App\Models\Location;
use App\Models\PickTask;
use App\Models\Product;
use App\Models\SalesOrder;
use App\Models\SalesOrderLine;
use App\Models\StockBalance;
use App\Models\Tenant;
use App\Models\Warehouse;
use App\Services\Outbound\WaveService;
use App\Support\Tenancy\TenantContext;

it('creates and releases a wave with optimized pick sequence', function () {
    $tenant = Tenant::factory()->create();
    app(TenantContext::class)->set($tenant);

    $warehouse = Warehouse::factory()->create();
    $product = Product::factory()->create();
    $location = Location::query()->create([
        'warehouse_id' => $warehouse->id,
        'code' => 'A-01-01',
        'aisle' => '01',
        'level' => '1',
        'pick_sequence' => 5,
    ]);

    StockBalance::query()->create([
        'warehouse_id' => $warehouse->id,
        'product_id' => $product->id,
        'location_id' => $location->id,
        'status' => StockBalanceStatus::Available,
        'qty' => 20,
        'reserved_qty' => 0,
    ]);

    $order = SalesOrder::query()->create([
        'warehouse_id' => $warehouse->id,
        'number' => 'SO-WAVE-1',
        'status' => SalesOrderStatus::Draft,
        'ordered_at' => now(),
    ]);

    SalesOrderLine::query()->create([
        'sales_order_id' => $order->id,
        'product_id' => $product->id,
        'line_number' => 1,
        'sku' => $product->sku,
        'qty_ordered' => 3,
    ]);

    $wave = app(WaveService::class)->create($warehouse->id, [$order->id], 'fifo');
    expect($wave->status)->toBe('planned');

    $released = app(WaveService::class)->release($wave->fresh());
    expect($released->status)->toBe('released')
        ->and(PickTask::query()->where('wave_id', $wave->id)->count())->toBeGreaterThan(0)
        ->and($order->fresh()->status)->toBe(SalesOrderStatus::Picking);
});
