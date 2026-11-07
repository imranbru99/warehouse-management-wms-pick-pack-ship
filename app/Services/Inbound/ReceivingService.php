<?php

declare(strict_types=1);

namespace App\Services\Inbound;

use App\Enums\Inbound\PurchaseOrderStatus;
use App\Enums\Inbound\PutawayTaskStatus;
use App\Enums\Inbound\ReceiptStatus;
use App\Enums\Inventory\StockLedgerType;
use App\Enums\Inventory\WarehouseTaskState;
use App\Enums\Inventory\WarehouseTaskType;
use App\Models\Location;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderLine;
use App\Models\PutawayTask;
use App\Models\Receipt;
use App\Models\ReceiptLine;
use App\Models\User;
use App\Models\WarehouseTask;
use App\Services\Inventory\StockLedgerService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReceivingService
{
    public function __construct(
        protected StockLedgerService $ledger,
        protected QcInspectionService $qc,
    ) {}

    /**
     * @param  array<int, array{purchase_order_line_id: int, qty: float|string, lot_code?: string|null, serial_code?: string|null, lpn_id?: int|null}>  $lines
     */
    public function receive(
        PurchaseOrder $purchaseOrder,
        array $lines,
        Location $dockLocation,
        ?User $user = null,
    ): Receipt {
        if ($dockLocation->warehouse_id !== $purchaseOrder->warehouse_id) {
            throw new \InvalidArgumentException('Dock location must belong to the purchase order warehouse.');
        }

        return DB::transaction(function () use ($purchaseOrder, $lines, $dockLocation, $user): Receipt {
            $receiptNumber = 'RCP-'.Str::upper(Str::random(8));

            $receipt = Receipt::query()->create([
                'tenant_id' => $purchaseOrder->tenant_id,
                'warehouse_id' => $purchaseOrder->warehouse_id,
                'purchase_order_id' => $purchaseOrder->id,
                'supplier_id' => $purchaseOrder->supplier_id,
                'number' => $receiptNumber,
                'status' => ReceiptStatus::Posted,
                'location_id' => $dockLocation->id,
                'received_by' => $user?->id,
                'received_at' => now(),
            ]);

            $lineNumber = 1;

            foreach ($lines as $lineInput) {
                /** @var PurchaseOrderLine $poLine */
                $poLine = PurchaseOrderLine::query()
                    ->where('purchase_order_id', $purchaseOrder->id)
                    ->whereKey($lineInput['purchase_order_line_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                $qty = (string) $lineInput['qty'];
                $remaining = bcsub((string) $poLine->qty_ordered, (string) $poLine->qty_received, 3);

                if (bccomp($qty, '0', 3) <= 0) {
                    throw new \InvalidArgumentException('Receive quantity must be positive.');
                }

                if (bccomp($qty, $remaining, 3) > 0) {
                    throw new \InvalidArgumentException('Receive quantity exceeds remaining on purchase order line.');
                }

                $receiptLine = ReceiptLine::query()->create([
                    'tenant_id' => $purchaseOrder->tenant_id,
                    'receipt_id' => $receipt->id,
                    'product_id' => $poLine->product_id,
                    'purchase_order_line_id' => $poLine->id,
                    'qty' => $qty,
                    'lot_code' => $lineInput['lot_code'] ?? '',
                    'serial_code' => $lineInput['serial_code'] ?? '',
                    'lpn_id' => $lineInput['lpn_id'] ?? null,
                    'line_number' => $lineNumber++,
                ]);

                $this->ledger->post([
                    'tenant_id' => $purchaseOrder->tenant_id,
                    'warehouse_id' => $purchaseOrder->warehouse_id,
                    'product_id' => $poLine->product_id,
                    'location_id' => $dockLocation->id,
                    'lot_code' => $receiptLine->lot_code,
                    'serial_code' => $receiptLine->serial_code,
                    'lpn_id' => $receiptLine->lpn_id,
                    'qty_delta' => $qty,
                    'type' => StockLedgerType::Receive,
                    'reason' => 'Inbound receive',
                    'reference_type' => ReceiptLine::class,
                    'reference_id' => $receiptLine->id,
                    'user_id' => $user?->id,
                ]);

                $newReceived = bcadd((string) $poLine->qty_received, $qty, 3);
                $poLine->update(['qty_received' => $newReceived]);

                $warehouseTask = WarehouseTask::query()->create([
                    'tenant_id' => $purchaseOrder->tenant_id,
                    'warehouse_id' => $purchaseOrder->warehouse_id,
                    'type' => WarehouseTaskType::Putaway,
                    'state' => WarehouseTaskState::Pending,
                    'reference_type' => ReceiptLine::class,
                    'reference_id' => $receiptLine->id,
                    'payload' => [
                        'receipt_id' => $receipt->id,
                        'qty' => $qty,
                    ],
                ]);

                PutawayTask::query()->create([
                    'tenant_id' => $purchaseOrder->tenant_id,
                    'warehouse_id' => $purchaseOrder->warehouse_id,
                    'warehouse_task_id' => $warehouseTask->id,
                    'receipt_id' => $receipt->id,
                    'receipt_line_id' => $receiptLine->id,
                    'product_id' => $poLine->product_id,
                    'from_location_id' => $dockLocation->id,
                    'qty' => $qty,
                    'lot_code' => $receiptLine->lot_code,
                    'serial_code' => $receiptLine->serial_code,
                    'lpn_id' => $receiptLine->lpn_id,
                    'status' => PutawayTaskStatus::Pending,
                ]);
            }

            $purchaseOrder->load('lines');
            $fullyReceived = $purchaseOrder->lines->every(
                fn (PurchaseOrderLine $line): bool => bccomp((string) $line->qty_received, (string) $line->qty_ordered, 3) >= 0
            );

            $purchaseOrder->update([
                'status' => $fullyReceived ? PurchaseOrderStatus::Received : PurchaseOrderStatus::Partial,
            ]);

            $this->qc->createForReceipt($receipt->fresh('lines'));

            return $receipt->load('lines');
        });
    }
}
