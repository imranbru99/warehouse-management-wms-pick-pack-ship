<?php

declare(strict_types=1);

namespace App\Services\Inbound;

use App\Enums\Inbound\QcInspectionStatus;
use App\Models\QcInspection;
use App\Models\Receipt;
use App\Models\ReceiptLine;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class QcInspectionService
{
    public function createForReceipt(Receipt $receipt): void
    {
        $receipt->loadMissing('lines');

        foreach ($receipt->lines as $line) {
            QcInspection::query()->firstOrCreate(
                [
                    'tenant_id' => $receipt->tenant_id,
                    'receipt_line_id' => $line->id,
                ],
                [
                    'warehouse_id' => $receipt->warehouse_id,
                    'receipt_id' => $receipt->id,
                    'product_id' => $line->product_id,
                    'status' => QcInspectionStatus::Pending,
                    'qty_inspected' => 0,
                    'qty_failed' => 0,
                ]
            );
        }
    }

    /**
     * @param  array{qty_inspected: float|string, qty_failed?: float|string, result: string, notes?: string|null}  $data
     */
    public function complete(QcInspection $inspection, array $data, ?User $inspector = null): QcInspection
    {
        if ($inspection->status === QcInspectionStatus::Completed) {
            throw new InvalidArgumentException('QC inspection already completed.');
        }

        $qtyInspected = (string) $data['qty_inspected'];
        $qtyFailed = (string) ($data['qty_failed'] ?? '0');

        if (bccomp($qtyInspected, '0', 3) <= 0) {
            throw new InvalidArgumentException('Inspected quantity must be positive.');
        }

        if (bccomp($qtyFailed, $qtyInspected, 3) > 0) {
            throw new InvalidArgumentException('Failed quantity cannot exceed inspected quantity.');
        }

        $result = $data['result'];
        if (! in_array($result, ['pass', 'fail', 'quarantine'], true)) {
            throw new InvalidArgumentException('Invalid QC result.');
        }

        return DB::transaction(function () use ($inspection, $qtyInspected, $qtyFailed, $result, $data, $inspector): QcInspection {
            $inspection->update([
                'status' => QcInspectionStatus::Completed,
                'result' => $result,
                'qty_inspected' => $qtyInspected,
                'qty_failed' => $qtyFailed,
                'inspector_id' => $inspector?->id,
                'inspected_at' => now(),
                'notes' => $data['notes'] ?? $inspection->notes,
            ]);

            return $inspection->fresh();
        });
    }

    public function start(QcInspection $inspection): QcInspection
    {
        if ($inspection->status !== QcInspectionStatus::Pending) {
            throw new InvalidArgumentException('Only pending inspections can be started.');
        }

        $inspection->update(['status' => QcInspectionStatus::InProgress]);

        return $inspection->fresh();
    }
}
