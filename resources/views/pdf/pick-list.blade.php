<!DOCTYPE html>
<html lang="{{ $locale ?? 'en' }}" dir="{{ in_array($locale ?? 'en', ['ar', 'he', 'fa'], true) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <title>Pick List {{ $order->number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        .meta { margin-bottom: 16px; color: #444; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        th { background: #f3f4f6; }
    </style>
</head>
<body>
    <h1>Pick List — {{ $order->number }}</h1>
    <div class="meta">
        <div>Warehouse: {{ $order->warehouse?->name }}</div>
        <div>Status: {{ $order->status->value }}</div>
        <div>Generated: {{ $generatedAt->toDateTimeString() }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>SKU</th>
                <th>Product</th>
                <th>Qty</th>
                <th>Location</th>
            </tr>
        </thead>
        <tbody>
            @forelse($order->pickTasks as $index => $task)
                <tr>
                    <td>{{ $task->pick_sequence ?? ($index + 1) }}</td>
                    <td>{{ $task->product?->sku ?? $task->sku }}</td>
                    <td>{{ $task->product?->name }}</td>
                    <td>{{ $task->qty }}</td>
                    <td>{{ $task->location?->code }}</td>
                </tr>
            @empty
                @foreach($order->lines as $index => $line)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $line->sku }}</td>
                        <td>{{ $line->product?->name }}</td>
                        <td>{{ $line->qty_ordered }}</td>
                        <td>—</td>
                    </tr>
                @endforeach
            @endforelse
        </tbody>
    </table>
</body>
</html>
