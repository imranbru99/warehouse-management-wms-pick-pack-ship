<?php

declare(strict_types=1);

use App\Enums\Inbound\PurchaseOrderStatus;
use App\Models\Location;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderLine;
use App\Models\PutawayTask;
use App\Models\StockBalance;
use App\Models\Supplier;
use App\Models\Tenant;
use App\Models\Warehouse;
use App\Services\Inbound\ReceivingService;
use App\Support\Tenancy\TenantContext;

beforeEach(function () {
    $this->tenant = Tenant::factory()->create();
    app(TenantContext::class)->set($this->tenant);

    $this->warehouse = Warehouse::factory()->create(['tenant_id' => $this->tenant->id]);
    $this->product = Product::factory()->create(['tenant_id' => $this->tenant->id]);
    $this->supplier = Supplier::query()->create([
        'tenant_id' => $this->tenant->id,
        'code' => 'SUP-001',
        'name' => 'Acme Supply',
        'status' => 'active',
    ]);

    $this->dock = Location::query()->create([
        'tenant_id' => $this->tenant->id,
        'warehouse_id' => $this->warehouse->id,
        'code' => 'DOCK-01',
        'type' => 'receiving',
        'status' => 'active',
    ]);
});

it('receives against a purchase order and updates dock balances', function () {
    $po = PurchaseOrder::query()->create([
        'tenant_id' => $this->tenant->id,
        'warehouse_id' => $this->warehouse->id,
        'supplier_id' => $this->supplier->id,
        'number' => 'PO-1001',
        'status' => PurchaseOrderStatus::Submitted,
    ]);

    $line = PurchaseOrderLine::query()->create([
        'tenant_id' => $this->tenant->id,
        'purchase_order_id' => $po->id,
        'product_id' => $this->product->id,
        'qty_ordered' => '100',
        'qty_received' => '0',
        'line_number' => 1,
    ]);

    $receiving = app(ReceivingService::class);

    $receipt = $receiving->receive($po, [
        ['purchase_order_line_id' => $line->id, 'qty' => '40'],
    ], $this->dock);

    expect($receipt->lines)->toHaveCount(1);

    $po->refresh();
    expect($po->status)->toBe(PurchaseOrderStatus::Partial)
        ->and((string) $line->fresh()->qty_received)->toBe('40.000');

    $balance = StockBalance::query()
        ->where('location_id', $this->dock->id)
        ->where('product_id', $this->product->id)
        ->first();

    expect($balance)->not->toBeNull()
        ->and((string) $balance->qty)->toBe('40.000');

    expect(PutawayTask::query()->where('receipt_id', $receipt->id)->count())->toBe(1);
});

it('allows partial receipts until the purchase order is fully received', function () {
    $po = PurchaseOrder::query()->create([
        'tenant_id' => $this->tenant->id,
        'warehouse_id' => $this->warehouse->id,
        'supplier_id' => $this->supplier->id,
        'number' => 'PO-1002',
        'status' => PurchaseOrderStatus::Submitted,
    ]);

    $line = PurchaseOrderLine::query()->create([
        'tenant_id' => $this->tenant->id,
        'purchase_order_id' => $po->id,
        'product_id' => $this->product->id,
        'qty_ordered' => '10',
        'qty_received' => '0',
        'line_number' => 1,
    ]);

    $receiving = app(ReceivingService::class);

    $receiving->receive($po, [
        ['purchase_order_line_id' => $line->id, 'qty' => '6'],
    ], $this->dock);

    $receiving->receive($po->fresh(), [
        ['purchase_order_line_id' => $line->id, 'qty' => '4'],
    ], $this->dock);

    $po->refresh();
    expect($po->status)->toBe(PurchaseOrderStatus::Received);

    $totalOnHand = StockBalance::query()
        ->where('product_id', $this->product->id)
        ->where('location_id', $this->dock->id)
        ->sum('qty');

    expect(bccomp((string) $totalOnHand, '10', 3))->toBe(0);
});
