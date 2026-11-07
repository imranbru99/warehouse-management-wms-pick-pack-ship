<?php

declare(strict_types=1);

namespace App\Services\Returns;

use App\Enums\Inventory\StockLedgerType;
use App\Enums\Returns\ReturnReceiptStatus;
use App\Enums\Returns\RmaLineDisposition;
use App\Enums\Returns\RmaRequestStatus;
use App\Models\Location;
use App\Models\ReturnReceipt;
use App\Models\RmaLine;
use App\Models\RmaRequest;
use App\Models\User;
use App\Services\Inventory\StockLedgerService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReturnService
{
    public function __construct(
        protected StockLedgerService $ledger,
    ) {}

    public function approve(RmaRequest $rma, ?User $user = null): RmaRequest
    {
        if ($rma->status === RmaRequestStatus::Cancelled) {
            throw new \InvalidArgumentException('Cancelled RMA cannot be approved.');
        }

        if ($rma->status === RmaRequestStatus::Approved || $rma->status === RmaRequestStatus::Receiving || $rma->status === RmaRequestStatus::Closed) {
            throw new \InvalidArgumentException('RMA is already approved.');
        }

        if ($rma->lines()->count() === 0) {
            throw new \InvalidArgumentException('RMA must have at least one line.');
        }

        $rma->update([
            'status' => RmaRequestStatus::Approved,
            'approved_by' => $user?->id,
            'approved_at' => now(),
        ]);

        return $rma->fresh();
    }

    /**
     * @param  array<int, array{rma_line_id: int, qty: float|string}>  $lines
     */
    public function receive(
        RmaRequest $rma,
        array $lines,
        Location $location,
        ?User $user = null,
    ): ReturnReceipt {
        if ($rma->status !== RmaRequestStatus::Approved && $rma->status !== RmaRequestStatus::Receiving) {
            throw new \InvalidArgumentException('RMA must be approved before receiving.');
        }

        if ($location->warehouse_id !== $rma->warehouse_id) {
            throw new \InvalidArgumentException('Receive location must belong to the RMA warehouse.');
        }

        return DB::transaction(function () use ($rma, $lines, $location, $user): ReturnReceipt {
            $receipt = ReturnReceipt::query()->create([
                'tenant_id' => $rma->tenant_id,
                'rma_request_id' => $rma->id,
                'warehouse_id' => $rma->warehouse_id,
                'location_id' => $location->id,
                'number' => 'RR-'.Str::upper(Str::random(8)),
                'status' => ReturnReceiptStatus::Posted,
                'received_by' => $user?->id,
                'received_at' => now(),
            ]);

            foreach ($lines as $lineInput) {
                /** @var RmaLine $rmaLine */
                $rmaLine = RmaLine::query()
                    ->where('rma_request_id', $rma->id)
                    ->whereKey($lineInput['rma_line_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                $qty = (string) $lineInput['qty'];
                $remaining = bcsub((string) $rmaLine->qty_requested, (string) $rmaLine->qty_received, 3);

                if (bccomp($qty, '0', 3) <= 0) {
                    throw new \InvalidArgumentException('Receive quantity must be positive.');
                }

                if (bccomp($qty, $remaining, 3) > 0) {
                    throw new \InvalidArgumentException('Receive quantity exceeds remaining on RMA line.');
                }

                $rmaLine->update([
                    'qty_received' => bcadd((string) $rmaLine->qty_received, $qty, 3),
                ]);
            }

            $rma->update(['status' => RmaRequestStatus::Receiving]);

            if ($this->allLinesFullyReceived($rma->fresh())) {
                $rma->update(['status' => RmaRequestStatus::Closed]);
            }

            return $receipt->fresh();
        });
    }

    public function applyDisposition(
        RmaLine $line,
        RmaLineDisposition $disposition,
        ?Location $restockLocation = null,
        ?User $user = null,
    ): RmaLine {
        if (bccomp((string) $line->qty_received, '0', 3) <= 0) {
            throw new \InvalidArgumentException('Cannot disposition a line with no received quantity.');
        }

        $rma = $line->rmaRequest;

        return DB::transaction(function () use ($line, $disposition, $restockLocation, $user, $rma): RmaLine {
            $restockable = bcsub((string) $line->qty_received, (string) $line->qty_restocked, 3);

            if ($disposition === RmaLineDisposition::Restock) {
                if ($restockLocation === null) {
                    throw new \InvalidArgumentException('Restock location is required for restock disposition.');
                }

                if ($restockLocation->warehouse_id !== $rma->warehouse_id) {
                    throw new \InvalidArgumentException('Restock location must belong to the RMA warehouse.');
                }

                if (bccomp($restockable, '0', 3) <= 0) {
                    throw new \InvalidArgumentException('No remaining quantity available to restock.');
                }

                $this->ledger->post([
                    'tenant_id' => $line->tenant_id,
                    'warehouse_id' => $rma->warehouse_id,
                    'product_id' => $line->product_id,
                    'location_id' => $restockLocation->id,
                    'qty_delta' => $restockable,
                    'type' => StockLedgerType::Return,
                    'reason' => 'rma_restock',
                    'reference_type' => RmaLine::class,
                    'reference_id' => $line->id,
                    'user_id' => $user?->id,
                    'meta' => ['rma_request_id' => $rma->id],
                ]);

                $line->update([
                    'disposition' => RmaLineDisposition::Restock,
                    'qty_restocked' => bcadd((string) $line->qty_restocked, $restockable, 3),
                ]);
            } else {
                $line->update(['disposition' => $disposition]);
            }

            return $line->fresh();
        });
    }

    private function allLinesFullyReceived(RmaRequest $rma): bool
    {
        foreach ($rma->lines as $line) {
            if (bccomp((string) $line->qty_received, (string) $line->qty_requested, 3) < 0) {
                return false;
            }
        }

        return $rma->lines->isNotEmpty();
    }
}
