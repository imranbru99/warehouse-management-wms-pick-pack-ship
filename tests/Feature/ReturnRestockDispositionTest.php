<?php

declare(strict_types=1);

use App\Enums\Returns\RmaLineDisposition;
use App\Enums\Returns\RmaRequestStatus;
use App\Models\Location;
use App\Models\Product;
use App\Models\RmaLine;
use App\Models\RmaRequest;
use App\Models\StockBalance;
use App\Models\Tenant;
use App\Models\Warehouse;
use App\Services\Returns\ReturnService;
use App\Support\Tenancy\TenantContext;

beforeEach(function () {
    $this->tenant = Tenant::factory()->create();
    app(TenantContext::class)->set($this->tenant);

    $this->warehouse = Warehouse::factory()->create(['tenant_id' => $this->tenant->id]);
    $this->product = Product::factory()->create(['tenant_id' => $this->tenant->id]);

    $this->returnsDock = Location::query()->create([
        'tenant_id' => $this->tenant->id,
        'warehouse_id' => $this->warehouse->id,
        'code' => 'RET-DOCK',
        'type' => 'receiving',
        'status' => 'active',
    ]);

    $this->storage = Location::query()->create([
        'tenant_id' => $this->tenant->id,
        'warehouse_id' => $this->warehouse->id,
        'code' => 'A-01-01',
        'type' => 'storage',
        'status' => 'active',
    ]);
});

it('restocks inventory when disposition is restock', function () {
    $rma = RmaRequest::query()->create([
        'tenant_id' => $this->tenant->id,
        'warehouse_id' => $this->warehouse->id,
        'number' => 'RMA-9001',
        'status' => RmaRequestStatus::Draft,
    ]);

    $line = RmaLine::query()->create([
        'tenant_id' => $this->tenant->id,
        'rma_request_id' => $rma->id,
        'product_id' => $this->product->id,
        'line_number' => 1,
        'qty_requested' => '4',
        'disposition' => RmaLineDisposition::Pending,
    ]);

    $service = app(ReturnService::class);

    $service->approve($rma->fresh());

    $service->receive($rma->fresh(), [
        ['rma_line_id' => $line->id, 'qty' => '4'],
    ], $this->returnsDock);

    $service->applyDisposition($line->fresh(), RmaLineDisposition::Restock, $this->storage);

    $line->refresh();
    expect($line->disposition)->toBe(RmaLineDisposition::Restock)
        ->and(bccomp((string) $line->qty_restocked, '4', 3))->toBe(0);

    $balance = StockBalance::query()
        ->where('location_id', $this->storage->id)
        ->where('product_id', $this->product->id)
        ->first();

    expect($balance)->not->toBeNull()
        ->and((string) $balance->qty)->toBe('4.000');
});
