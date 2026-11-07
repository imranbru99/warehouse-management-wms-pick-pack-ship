<?php

declare(strict_types=1);

namespace App\Services\Inbound;

use App\Enums\Inbound\PutawayTaskStatus;
use App\Enums\Inventory\StockLedgerType;
use App\Enums\Inventory\WarehouseTaskState;
use App\Models\Location;
use App\Models\PutawayTask;
use App\Models\User;
use App\Services\Inventory\StockLedgerService;
use Illuminate\Support\Facades\DB;

class PutawayService
{
    public function __construct(
        protected StockLedgerService $ledger,
    ) {}

    /**
     * Suggest a storage / pick-face location for directed putaway.
     * Priority: matching zone capacity with pick-face first, then any open storage bin.
     */
    public function suggestLocation(PutawayTask $task): ?Location
    {
        $base = Location::query()
            ->where('warehouse_id', $task->warehouse_id)
            ->where('is_blocked', false)
            ->whereIn('type', ['storage', 'pick_face', 'forward_pick', 'reserve']);

        $pickFace = (clone $base)
            ->where('is_pick_face', true)
            ->orderBy('pick_sequence')
            ->first();

        if ($pickFace !== null) {
            return $pickFace;
        }

        return $base->orderBy('pick_sequence')->first();
    }

    public function assignSuggested(PutawayTask $task): PutawayTask
    {
        $location = $this->suggestLocation($task);

        if ($location === null) {
            throw new \InvalidArgumentException('No eligible putaway location found in this warehouse.');
        }

        $task->update([
            'to_location_id' => $location->id,
            'status' => PutawayTaskStatus::Assigned,
        ]);

        return $task->fresh();
    }

    public function complete(PutawayTask $task, Location $toLocation, ?User $user = null): PutawayTask
    {
        if ($task->status === PutawayTaskStatus::Completed) {
            throw new \InvalidArgumentException('Putaway task is already completed.');
        }

        if ($toLocation->warehouse_id !== $task->warehouse_id) {
            throw new \InvalidArgumentException('Destination location must belong to the task warehouse.');
        }

        return DB::transaction(function () use ($task, $toLocation, $user): PutawayTask {
            $qty = (string) $task->qty;

            $this->ledger->post([
                'tenant_id' => $task->tenant_id,
                'warehouse_id' => $task->warehouse_id,
                'product_id' => $task->product_id,
                'location_id' => $task->from_location_id,
                'lot_code' => $task->lot_code,
                'serial_code' => $task->serial_code,
                'lpn_id' => $task->lpn_id,
                'qty_delta' => bcmul($qty, '-1', 3),
                'type' => StockLedgerType::Putaway,
                'reason' => 'Putaway move from dock',
                'reference_type' => PutawayTask::class,
                'reference_id' => $task->id,
                'user_id' => $user?->id,
                'meta' => ['leg' => 'from'],
            ]);

            $this->ledger->post([
                'tenant_id' => $task->tenant_id,
                'warehouse_id' => $task->warehouse_id,
                'product_id' => $task->product_id,
                'location_id' => $toLocation->id,
                'lot_code' => $task->lot_code,
                'serial_code' => $task->serial_code,
                'lpn_id' => $task->lpn_id,
                'qty_delta' => $qty,
                'type' => StockLedgerType::Putaway,
                'reason' => 'Putaway move to storage',
                'reference_type' => PutawayTask::class,
                'reference_id' => $task->id,
                'user_id' => $user?->id,
                'meta' => ['leg' => 'to'],
            ]);

            $task->update([
                'to_location_id' => $toLocation->id,
                'status' => PutawayTaskStatus::Completed,
                'completed_at' => now(),
            ]);

            if ($task->warehouse_task_id) {
                $task->warehouseTask?->update([
                    'state' => WarehouseTaskState::Completed,
                    'completed_at' => now(),
                ]);
            }

            return $task->fresh();
        });
    }
}
