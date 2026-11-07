<?php

declare(strict_types=1);

namespace App\Services\Outbound;

use App\Enums\SalesOrderStatus;
use App\Enums\ShipmentStatus;
use App\Models\Allocation;
use App\Models\Shipment;
use App\Models\TrackingEvent;
use App\Services\Inventory\StockLedgerService;
use App\Services\Outbound\Shipping\CarrierAdapter;
use App\Services\Outbound\Shipping\GenericRestCarrierAdapter;
use App\Services\Outbound\Shipping\ManualLocalCourierAdapter;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ShippingService
{
    /** @var array<string, CarrierAdapter> */
    private array $carriers;

    public function __construct(
        private StockLedgerService $ledger,
        ?ManualLocalCourierAdapter $local = null,
        ?GenericRestCarrierAdapter $genericRest = null,
    ) {
        $local ??= new ManualLocalCourierAdapter;
        $genericRest ??= new GenericRestCarrierAdapter;
        $this->carriers = [
            $local->key() => $local,
            $genericRest->key() => $genericRest,
        ];
    }

    public function registerCarrier(CarrierAdapter $adapter): void
    {
        $this->carriers[$adapter->key()] = $adapter;
    }

    public function createLabel(Shipment $shipment, string $carrierKey = 'local'): Shipment
    {
        $adapter = $this->carriers[$carrierKey] ?? throw new InvalidArgumentException("Unknown carrier [{$carrierKey}]");

        DB::transaction(function () use ($shipment, $adapter, $carrierKey) {
            $result = $adapter->createShipment($shipment);

            $shipment->carrier = $carrierKey;
            $shipment->tracking_number = $result['tracking_number'];
            $shipment->meta = array_merge($shipment->meta ?? [], ['label' => $result]);

            if ($shipment->status === ShipmentStatus::Packed) {
                $shipment->transitionTo(ShipmentStatus::LabelCreated);
            } else {
                $shipment->save();
            }
        });

        return $shipment->fresh();
    }

    public function confirmShip(Shipment $shipment): Shipment
    {
        return DB::transaction(function () use ($shipment) {
            $shipment->loadMissing('salesOrder.lines.allocations');

            if ($shipment->status !== ShipmentStatus::LabelCreated && $shipment->status !== ShipmentStatus::Packed) {
                if (! $shipment->canTransitionTo(ShipmentStatus::Shipped)) {
                    throw new InvalidArgumentException('Shipment is not ready to ship.');
                }
            }

            $order = $shipment->salesOrder;

            foreach ($order->lines as $line) {
                foreach ($line->allocations()->where('status', 'allocated')->get() as $allocation) {
                    /** @var Allocation $allocation */
                    $this->ledger->deductForShipment($allocation, $shipment);
                    $allocation->status = 'shipped';
                    $allocation->save();

                    $line->qty_shipped = (float) $line->qty_shipped + (float) $allocation->qty;
                    $line->save();
                }
            }

            $shipment->transitionTo(ShipmentStatus::Shipped);

            TrackingEvent::query()->create([
                'shipment_id' => $shipment->id,
                'occurred_at' => now(),
                'status' => 'shipped',
                'description' => 'Shipment confirmed and dispatched',
            ]);

            if ($order->canTransitionTo(SalesOrderStatus::Shipped)) {
                $order->transitionTo(SalesOrderStatus::Shipped);
            }

            return $shipment->fresh('trackingEvents');
        });
    }
}
