<?php

declare(strict_types=1);

use App\Enums\Inventory\StockBalanceStatus;
use App\Enums\Inventory\StockLedgerType;
use App\Enums\SalesOrderStatus;
use App\Models\Location;
use App\Models\Product;
use App\Models\SalesOrder;
use App\Models\SalesOrderLine;
use App\Models\StockBalance;
use App\Models\StockLedgerEntry;
use App\Models\Tenant;
use App\Models\Warehouse;
use App\Services\Outbound\AllocationService;
use App\Services\Outbound\PackService;
use App\Services\Outbound\ShippingService;
use App\Support\Tenancy\TenantContext;

it('ships an allocated order and posts stock ledger deductions', function () {
    $tenant = Tenant::factory()->create();
    app(TenantContext::class)->set($tenant);

    $warehouse = Warehouse::factory()->create();
    $product = Product::factory()->create(['length' => 5, 'width' => 5, 'height' => 5]);
    $location = Location::query()->create([
        'warehouse_id' => $warehouse->id,
        'code' => 'PICK-01',
        'pick_sequence' => 1,
    ]);

    $balance = StockBalance::query()->create([
        'warehouse_id' => $warehouse->id,
        'product_id' => $product->id,
        'location_id' => $location->id,
        'status' => StockBalanceStatus::Available,
        'qty' => 10,
        'reserved_qty' => 0,
    ]);

    $order = SalesOrder::query()->create([
        'warehouse_id' => $warehouse->id,
        'number' => 'SO-SHIP-1',
        'status' => SalesOrderStatus::Draft,
        'ordered_at' => now(),
    ]);

    SalesOrderLine::query()->create([
        'sales_order_id' => $order->id,
        'product_id' => $product->id,
        'line_number' => 1,
        'sku' => $product->sku,
        'qty_ordered' => 4,
    ]);

    app(AllocationService::class)->allocate($order->fresh('lines'), 'fifo');

    $shipment = app(PackService::class)->packOrder($order->fresh(), [
        'length' => 50,
        'width' => 50,
        'height' => 50,
    ]);

    app(ShippingService::class)->createLabel($shipment->fresh());
    app(ShippingService::class)->confirmShip($shipment->fresh());

    $balance->refresh();
    $order->refresh();

    expect((float) $balance->qty)->toBe(6.0)
        ->and((float) $balance->reserved_qty)->toBe(0.0)
        ->and($order->status)->toBe(SalesOrderStatus::Shipped);

    $ledger = StockLedgerEntry::query()
        ->where('product_id', $product->id)
        ->where('type', StockLedgerType::Ship)
        ->first();

    expect($ledger)->not->toBeNull()
        ->and((float) $ledger->qty_delta)->toBe(-4.0);
});
