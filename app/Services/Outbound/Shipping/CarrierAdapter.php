<?php

declare(strict_types=1);

namespace App\Services\Outbound\Shipping;

use App\Models\Shipment;

interface CarrierAdapter
{
    public function key(): string;

    /**
     * @return array{tracking_number: string, label_url: ?string, raw: ?array<string, mixed>}
     */
    public function createShipment(Shipment $shipment): array;

    /**
     * @return array<int, array{status: string, description: ?string, occurred_at: string, location: ?string}>
     */
    public function fetchTrackingEvents(Shipment $shipment): array;
}
