<?php

declare(strict_types=1);

use App\Enums\Inventory\StockBalanceStatus;
use App\Enums\SalesOrderStatus;
use App\Models\Location;
use App\Models\Product;
use App\Models\SalesOrder;
use App\Models\SalesOrderLine;
use App\Models\StockBalance;
use App\Models\Tenant;
use App\Models\Warehouse;
use App\Services\Outbound\AllocationService;
use App\Support\Tenancy\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('allocates stock in FIFO order by balance created_at', function () {
    $tenant = Tenant::factory()->create();
    app(TenantContext::class)->set($tenant);

    $warehouse = Warehouse::factory()->create();
    $product = Product::factory()->create();
    $locationA = Location::query()->create([
        'warehouse_id' => $warehouse->id,
        'code' => 'A-01',
        'aisle' => '01',
        'level' => '1',
        'pick_sequence' => 10,
    ]);
    $locationB = Location::query()->create([
        'warehouse_id' => $warehouse->id,
        'code' => 'B-01',
        'aisle' => '02',
        'level' => '1',
        'pick_sequence' => 20,
    ]);

    $older = StockBalance::query()->create([
        'warehouse_id' => $warehouse->id,
        'product_id' => $product->id,
        'location_id' => $locationB->id,
        'status' => StockBalanceStatus::Available,
        'qty' => 5,
        'reserved_qty' => 0,
        'created_at' => now()->subDay(),
    ]);

    $newer = StockBalance::query()->create([
        'warehouse_id' => $warehouse->id,
        'product_id' => $product->id,
        'location_id' => $locationA->id,
        'status' => StockBalanceStatus::Available,
        'qty' => 5,
        'reserved_qty' => 0,
        'created_at' => now(),
    ]);

    $order = SalesOrder::query()->create([
        'warehouse_id' => $warehouse->id,
        'number' => 'SO-FIFO-1',
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

    app(AllocationService::class)->allocate($order->fresh('lines'), 'fifo');

    $allocation = $order->fresh('lines')->lines->first()->allocations->first();

    expect($allocation->stock_balance_id)->toBe($older->id)
        ->and($allocation->location_id)->toBe($locationB->id);

    $older->refresh();
    $newer->refresh();

    expect((float) $older->reserved_qty)->toBe(3.0)
        ->and((float) $newer->reserved_qty)->toBe(0.0);
});
