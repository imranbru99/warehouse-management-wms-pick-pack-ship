<?php

declare(strict_types=1);

namespace App\Services\Outbound\Shipping;

use App\Models\Shipment;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class GenericRestCarrierAdapter implements CarrierAdapter
{
    public function __construct(
        private ?string $baseUrl = null,
        private ?string $apiKey = null,
    ) {
        $this->baseUrl ??= config('services.carrier_rest.url');
        $this->apiKey ??= config('services.carrier_rest.key');
    }

    public function key(): string
    {
        return 'generic_rest';
    }

    public function createShipment(Shipment $shipment): array
    {
        if (! $this->baseUrl) {
            throw new RuntimeException('Carrier REST base URL is not configured.');
        }

        $response = Http::withToken((string) $this->apiKey)
            ->acceptJson()
            ->post(rtrim($this->baseUrl, '/').'/shipments', [
                'reference' => $shipment->number,
                'ship_to' => $shipment->ship_to,
                'packages' => $shipment->cartons()->count(),
            ]);

        if (! $response->successful()) {
            throw new RuntimeException('Carrier API error: '.$response->body());
        }

        $data = $response->json();

        return [
            'tracking_number' => (string) ($data['tracking_number'] ?? $data['id'] ?? ''),
            'label_url' => $data['label_url'] ?? null,
            'raw' => is_array($data) ? $data : null,
        ];
    }

    public function fetchTrackingEvents(Shipment $shipment): array
    {
        if (! $this->baseUrl || ! $shipment->tracking_number) {
            return [];
        }

        $response = Http::withToken((string) $this->apiKey)
            ->acceptJson()
            ->get(rtrim($this->baseUrl, '/').'/track/'.$shipment->tracking_number);

        if (! $response->successful()) {
            return [];
        }

        $events = $response->json('events', []);

        return is_array($events) ? $events : [];
    }
}
