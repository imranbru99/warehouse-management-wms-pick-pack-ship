<?php

declare(strict_types=1);

namespace App\Services\Outbound\Shipping;

use App\Models\Shipment;
use Illuminate\Support\Str;

class ManualLocalCourierAdapter implements CarrierAdapter
{
    public function key(): string
    {
        return 'local';
    }

    public function createShipment(Shipment $shipment): array
    {
        $tracking = $shipment->tracking_number ?? 'LOC-'.Str::upper(Str::random(12));

        return [
            'tracking_number' => $tracking,
            'label_url' => null,
            'raw' => ['carrier' => 'local', 'manual' => true],
        ];
    }

    public function fetchTrackingEvents(Shipment $shipment): array
    {
        if ($shipment->shipped_at === null) {
            return [];
        }

        return [
            [
                'status' => 'picked_up',
                'description' => 'Shipment picked up by local courier',
                'occurred_at' => $shipment->shipped_at->toIso8601String(),
                'location' => $shipment->warehouse?->name,
            ],
        ];
    }
}
