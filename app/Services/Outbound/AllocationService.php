<?php

declare(strict_types=1);

namespace App\Services\Outbound;

use App\Enums\SalesOrderStatus;
use App\Models\Allocation;
use App\Models\PickTask;
use App\Models\SalesOrder;
use App\Models\StockBalance;
use App\Services\Outbound\Allocation\AllocationStrategy;
use App\Services\Outbound\Allocation\FefoStrategy;
use App\Services\Outbound\Allocation\FifoStrategy;
use App\Services\Outbound\Allocation\NearestBinStrategy;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class AllocationService
{
    /** @var array<string, AllocationStrategy> */
    private array $strategies;

    public function __construct(
        ?FifoStrategy $fifo = null,
        ?FefoStrategy $fefo = null,
        ?NearestBinStrategy $nearest = null,
    ) {
        $fifo ??= new FifoStrategy;
        $fefo ??= new FefoStrategy;
        $nearest ??= new NearestBinStrategy;

        $this->strategies = [
            $fifo->key() => $fifo,
            $fefo->key() => $fefo,
            $nearest->key() => $nearest,
        ];
    }

    public function strategy(string $key): AllocationStrategy
    {
        if (! isset($this->strategies[$key])) {
            throw new InvalidArgumentException("Unknown allocation strategy [{$key}]");
        }

        return $this->strategies[$key];
    }

    public function allocate(SalesOrder $order, string $strategyKey = 'fifo'): SalesOrder
    {
        $strategy = $this->strategy($strategyKey);

        if ($order->status === SalesOrderStatus::Draft) {
            $order->confirm();
        }

        DB::transaction(function () use ($order, $strategy, $strategyKey) {
            $order->loadMissing('lines.product');

            foreach ($order->lines as $line) {
                $remaining = $line->qtyRemainingToAllocate();
                if ($remaining <= 0) {
                    continue;
                }

                $balances = StockBalance::query()
                    ->with('location')
                    ->where('warehouse_id', $order->warehouse_id)
                    ->where('product_id', $line->product_id)
                    ->where('status', 'available')
                    ->lockForUpdate()
                    ->get();

                $sorted = $strategy->sortBalances($balances);

                foreach ($sorted as $balance) {
                    if ($remaining <= 0) {
                        break;
                    }

                    $available = $balance->availableQty();
                    if ($available <= 0) {
                        continue;
                    }

                    $take = min($remaining, $available);

                    $allocation = Allocation::query()->create([
                        'sales_order_line_id' => $line->id,
                        'product_id' => $line->product_id,
                        'location_id' => $balance->location_id,
                        'stock_balance_id' => $balance->id,
                        'lot_code' => $balance->lot_code,
                        'qty' => $take,
                        'status' => 'allocated',
                        'strategy' => $strategyKey,
                    ]);

                    $balance->reserved_qty = (float) $balance->reserved_qty + $take;
                    $balance->save();

                    $line->qty_allocated = (float) $line->qty_allocated + $take;
                    $line->save();

                    PickTask::query()->create([
                        'allocation_id' => $allocation->id,
                        'sales_order_id' => $order->id,
                        'location_id' => $balance->location_id,
                        'product_id' => $line->product_id,
                        'qty' => $take,
                        'pick_sequence' => $balance->location !== null ? $balance->location->pick_sequence : 0,
                        'status' => 'pending',
                    ]);

                    $remaining -= $take;
                }

                if ($remaining > 0) {
                    throw new InvalidArgumentException(
                        "Insufficient stock for SKU {$line->sku}: short {$remaining} units"
                    );
                }
            }

            if ($order->status === SalesOrderStatus::Confirmed) {
                $order->transitionTo(SalesOrderStatus::Allocated);
            }
        });

        return $order->fresh(['lines']);
    }
}
