<?php

declare(strict_types=1);

namespace App\Services\Outbound;

use App\Enums\SalesOrderStatus;
use App\Enums\ShipmentStatus;
use App\Models\Carton;
use App\Models\Product;
use App\Models\SalesOrder;
use App\Models\Shipment;
use Illuminate\Support\Facades\DB;

class PackService
{
    public function __construct(private CartonizationService $cartonization) {}

    /**
     * @param  array{length: float, width: float, height: float}  $cartonSpec
     */
    public function packOrder(SalesOrder $order, array $cartonSpec): Shipment
    {
        return DB::transaction(function () use ($order, $cartonSpec) {
            $order->loadMissing('lines.product');

            $shipment = Shipment::query()->create([
                'warehouse_id' => $order->warehouse_id,
                'sales_order_id' => $order->id,
                'number' => 'SHP-'.$order->number,
                'status' => ShipmentStatus::Draft,
                'ship_to' => $order->shipping_address,
            ]);

            $items = $order->lines->flatMap(function ($line) {
                /** @var Product $product */
                $product = $line->product;

                return [[
                    'id' => $line->product_id,
                    'length' => (float) ($product->length ?? 10),
                    'width' => (float) ($product->width ?? 10),
                    'height' => (float) ($product->height ?? 10),
                    'qty' => (int) ceil((float) $line->qty_ordered),
                ]];
            })->all();

            $packed = $this->cartonization->pack($items, $cartonSpec);

            foreach ($packed as $index => $contents) {
                Carton::query()->create([
                    'shipment_id' => $shipment->id,
                    'code' => sprintf('CTN-%s-%02d', $shipment->number, $index + 1),
                    'length' => $cartonSpec['length'],
                    'width' => $cartonSpec['width'],
                    'height' => $cartonSpec['height'],
                    'status' => 'packed',
                    'contents' => $contents,
                ]);
            }

            $shipment->transitionTo(ShipmentStatus::Packed);

            $this->advanceOrderToPacked($order);

            return $shipment->load('cartons');
        });
    }

    private function advanceOrderToPacked(SalesOrder $order): void
    {
        if ($order->status === SalesOrderStatus::Packed) {
            return;
        }

        $steps = [
            SalesOrderStatus::Picking,
            SalesOrderStatus::Picked,
            SalesOrderStatus::Packed,
        ];

        foreach ($steps as $step) {
            if ($order->status === $step) {
                continue;
            }
            if ($order->canTransitionTo($step)) {
                $order->transitionTo($step);
            }
        }
    }
}
