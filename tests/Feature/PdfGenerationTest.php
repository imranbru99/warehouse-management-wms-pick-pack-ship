<?php

declare(strict_types=1);

use App\Enums\SalesOrderStatus;
use App\Models\Product;
use App\Models\SalesOrder;
use App\Models\SalesOrderLine;
use App\Models\Tenant;
use App\Models\Warehouse;
use App\Services\Documents\PickListPdfService;
use App\Support\Tenancy\TenantContext;
use ImranDev\UnicodePdf\Facades\UnicodePdf;

it('generates pick list PDFs for english and bangla locales', function () {
    UnicodePdf::fake();

    $tenant = Tenant::factory()->create();
    app(TenantContext::class)->set($tenant);

    $warehouse = Warehouse::factory()->create(['name' => 'মূল গুদাম']);
    $product = Product::factory()->create(['name' => 'পণ্য এক', 'sku' => 'BN-001']);

    $order = SalesOrder::query()->create([
        'warehouse_id' => $warehouse->id,
        'number' => 'SO-PDF-1',
        'status' => SalesOrderStatus::Allocated,
        'ordered_at' => now(),
    ]);

    SalesOrderLine::query()->create([
        'sales_order_id' => $order->id,
        'product_id' => $product->id,
        'line_number' => 1,
        'sku' => $product->sku,
        'qty_ordered' => 2,
    ]);

    $service = app(PickListPdfService::class);

    expect($service->output($order->fresh(['lines.product', 'warehouse']), 'en'))->toBeString()
        ->and($service->output($order->fresh(['lines.product', 'warehouse']), 'bn'))->toBeString();
});
