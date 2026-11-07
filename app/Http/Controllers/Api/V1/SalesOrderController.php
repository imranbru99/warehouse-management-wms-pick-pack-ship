<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\SalesOrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\SalesOrder;
use App\Models\SalesOrderLine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SalesOrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $orders = SalesOrder::query()
            ->with(['customer', 'lines'])
            ->when($request->query('status'), fn ($q, $status) => $q->where('status', $status))
            ->orderByDesc('id')
            ->paginate((int) $request->query('per_page', 25));

        return response()->json($orders);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'warehouse_id' => ['required', 'integer', 'exists:warehouses,id'],
            'customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'number' => ['nullable', 'string', 'max:64'],
            'external_ref' => ['nullable', 'string', 'max:128'],
            'priority' => ['nullable', 'integer', 'min:1', 'max:9'],
            'shipping_address' => ['nullable', 'array'],
            'notes' => ['nullable', 'string'],
            'lines' => ['required', 'array', 'min:1'],
            'lines.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'lines.*.qty_ordered' => ['required', 'numeric', 'gt:0'],
            'lines.*.unit_price' => ['nullable', 'numeric', 'gte:0'],
        ]);

        $number = $validated['number'] ?? 'SO-'.Str::upper(Str::random(10));

        $order = SalesOrder::query()->create([
            'warehouse_id' => $validated['warehouse_id'],
            'customer_id' => $validated['customer_id'] ?? null,
            'number' => $number,
            'external_ref' => $validated['external_ref'] ?? null,
            'status' => SalesOrderStatus::Draft,
            'priority' => $validated['priority'] ?? 5,
            'ordered_at' => now(),
            'shipping_address' => $validated['shipping_address'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        foreach ($validated['lines'] as $index => $line) {
            $product = Product::query()->findOrFail($line['product_id']);

            SalesOrderLine::query()->create([
                'sales_order_id' => $order->id,
                'product_id' => $product->id,
                'line_number' => $index + 1,
                'sku' => $product->sku,
                'qty_ordered' => $line['qty_ordered'],
                'unit_price' => $line['unit_price'] ?? null,
                'status' => 'open',
            ]);
        }

        return response()->json($order->load('lines'), 201);
    }
}
