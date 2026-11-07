<?php

declare(strict_types=1);

namespace App\Services\Outbound;

use App\Enums\SalesOrderStatus;
use App\Models\PickTask;
use App\Models\SalesOrder;
use App\Models\Wave;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class WaveService
{
    public function __construct(
        private AllocationService $allocation,
        private PickPathOptimizer $pickPath,
    ) {}

    /**
     * @param  list<int>  $salesOrderIds
     */
    public function create(int $warehouseId, array $salesOrderIds, string $strategy = 'fifo'): Wave
    {
        return DB::transaction(function () use ($warehouseId, $salesOrderIds, $strategy): Wave {
            $orders = SalesOrder::query()
                ->where('warehouse_id', $warehouseId)
                ->whereIn('id', $salesOrderIds)
                ->lockForUpdate()
                ->get();

            if ($orders->isEmpty()) {
                throw new InvalidArgumentException('No sales orders selected for the wave.');
            }

            $wave = Wave::query()->create([
                'warehouse_id' => $warehouseId,
                'code' => 'WAV-'.Str::upper(Str::random(8)),
                'status' => 'planned',
            ]);

            foreach ($orders as $order) {
                if (in_array($order->status, [SalesOrderStatus::Draft, SalesOrderStatus::Confirmed], true)) {
                    $this->allocation->allocate($order->fresh('lines'), $strategy);
                    $order->refresh();
                }

                $order->update(['meta' => array_merge($order->meta ?? [], ['wave_id' => $wave->id])]);
                PickTask::query()
                    ->where('sales_order_id', $order->id)
                    ->whereNull('wave_id')
                    ->update(['wave_id' => $wave->id]);
            }

            return $wave->fresh();
        });
    }

    public function release(Wave $wave): Wave
    {
        if ($wave->status === 'released') {
            return $wave;
        }

        return DB::transaction(function () use ($wave): Wave {
            $tasks = PickTask::query()
                ->where('wave_id', $wave->id)
                ->with('location')
                ->get();

            $ordered = $this->pickPath->optimizeTasks($tasks);

            foreach ($ordered as $task) {
                $task->save();
                $task->update(['status' => 'released']);

                $order = $task->salesOrder;
                if ($order !== null && $order->status === SalesOrderStatus::Allocated && $order->canTransitionTo(SalesOrderStatus::Picking)) {
                    $order->transitionTo(SalesOrderStatus::Picking);
                }
            }

            $wave->update([
                'status' => 'released',
                'released_at' => now(),
            ]);

            return $wave->fresh();
        });
    }
}
