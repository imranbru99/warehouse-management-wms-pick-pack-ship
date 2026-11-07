<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Track {{ $shipment->tracking_number ?? $shipment->number }}</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 2rem; color: #1e293b; }
        h1 { font-size: 1.5rem; }
        .badge { display: inline-block; padding: 0.25rem 0.75rem; border-radius: 999px; background: #e2e8f0; font-size: 0.875rem; }
        ul { list-style: none; padding: 0; }
        li { padding: 0.75rem 0; border-bottom: 1px solid #e2e8f0; }
        time { color: #64748b; font-size: 0.875rem; }
    </style>
</head>
<body>
    <h1>Shipment {{ $shipment->number }}</h1>
    <p>
        <span class="badge">{{ $shipment->status instanceof \BackedEnum ? $shipment->status->value : $shipment->status }}</span>
        @if($shipment->tracking_number)
            &middot; Tracking: <strong>{{ $shipment->tracking_number }}</strong>
        @endif
    </p>
    @if($shipment->salesOrder)
        <p>Order: {{ $shipment->salesOrder->number }}</p>
    @endif

    <h2>Events</h2>
    <ul>
        @forelse($shipment->trackingEvents as $event)
            <li>
                <strong>{{ $event->status }}</strong>
                @if($event->description)
                    — {{ $event->description }}
                @endif
                <br>
                <time>{{ $event->occurred_at->toDayDateTimeString() }}</time>
                @if($event->location)
                    &middot; {{ $event->location }}
                @endif
            </li>
        @empty
            <li>No tracking events yet.</li>
        @endforelse
    </ul>
</body>
</html>
