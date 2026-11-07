<?php

declare(strict_types=1);

namespace App\Services\Inventory;

use App\Enums\Inventory\StockAdjustmentStatus;
use App\Enums\Inventory\StockLedgerType;
use App\Models\StockAdjustment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class StockAdjustmentService
{
    public function __construct(
        protected StockLedgerService $ledger,
    ) {}

    public function post(StockAdjustment $adjustment, ?User $user = null): StockAdjustment
    {
        if ($adjustment->status === StockAdjustmentStatus::Posted) {
            throw new \InvalidArgumentException('Adjustment is already posted.');
        }

        return DB::transaction(function () use ($adjustment, $user): StockAdjustment {
            foreach ($adjustment->lines as $line) {
                $this->ledger->post([
                    'tenant_id' => $adjustment->tenant_id,
                    'warehouse_id' => $adjustment->warehouse_id,
                    'product_id' => $line->product_id,
                    'location_id' => $line->location_id,
                    'lot_code' => $line->lot_code,
                    'serial_code' => $line->serial_code,
                    'lpn_id' => $line->lpn_id,
                    'qty_delta' => (string) $line->qty_delta,
                    'type' => StockLedgerType::Adjust,
                    'balance_status' => $line->balance_status,
                    'reason' => $adjustment->reason,
                    'reference_type' => StockAdjustment::class,
                    'reference_id' => $adjustment->id,
                    'user_id' => $user?->id,
                ]);
            }

            $adjustment->update([
                'status' => StockAdjustmentStatus::Posted,
                'posted_by' => $user?->id,
                'posted_at' => now(),
            ]);

            return $adjustment->fresh();
        });
    }
}
