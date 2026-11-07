<?php

declare(strict_types=1);

namespace App\Services\Inventory;

use App\Enums\Inventory\StockBalanceStatus;
use App\Enums\Inventory\StockLedgerType;
use App\Exceptions\InsufficientStockException;
use App\Models\Allocation;
use App\Models\StockBalance;
use App\Models\StockLedgerEntry;
use App\Support\Tenancy\TenantContext;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class StockLedgerService
{
    /**
     * @param  array{
     *     tenant_id?: int|null,
     *     warehouse_id: int,
     *     product_id: int,
     *     location_id: int,
     *     lot_code?: string|null,
     *     serial_code?: string|null,
     *     lpn_id?: int|null,
     *     qty_delta: float|string,
     *     type: StockLedgerType|string,
     *     balance_status?: StockBalanceStatus|string|null,
     *     reason?: string|null,
     *     reference_type?: string|null,
     *     reference_id?: int|null,
     *     user_id?: int|null,
     *     device_id?: int|null,
     *     meta?: array<string, mixed>|null,
     * }  $entry
     */
    public function post(array $entry): StockLedgerEntry
    {
        return DB::transaction(function () use ($entry): StockLedgerEntry {
            $tenantId = $entry['tenant_id'] ?? app(TenantContext::class)->id();

            if ($tenantId === null) {
                throw new \InvalidArgumentException('Tenant context is required to post stock ledger entries.');
            }

            $type = $entry['type'] instanceof StockLedgerType
                ? $entry['type']
                : StockLedgerType::from((string) $entry['type']);

            $balanceStatus = $entry['balance_status'] ?? StockBalanceStatus::Available;
            if (! $balanceStatus instanceof StockBalanceStatus) {
                $balanceStatus = StockBalanceStatus::from((string) $balanceStatus);
            }

            $lotCode = $this->normalizeCode($entry['lot_code'] ?? null);
            $serialCode = $this->normalizeCode($entry['serial_code'] ?? null);
            $lpnId = $entry['lpn_id'] ?? null;
            $qtyDelta = (string) $entry['qty_delta'];

            $balance = $this->lockBalance(
                tenantId: (int) $tenantId,
                warehouseId: (int) $entry['warehouse_id'],
                productId: (int) $entry['product_id'],
                locationId: (int) $entry['location_id'],
                lotCode: $lotCode,
                serialCode: $serialCode,
                lpnId: $lpnId,
                status: $balanceStatus,
            );

            $qtyBefore = $balance !== null ? (string) $balance->qty : '0';
            $qtyAfter = bcadd($qtyBefore, $qtyDelta, 3);

            if (bccomp($qtyAfter, '0', 3) < 0 && ! config('wms.allow_negative_stock', false)) {
                throw new InsufficientStockException('Stock quantity cannot go negative.');
            }

            if ($balance === null) {
                $balance = StockBalance::query()->create([
                    'tenant_id' => $tenantId,
                    'warehouse_id' => $entry['warehouse_id'],
                    'product_id' => $entry['product_id'],
                    'location_id' => $entry['location_id'],
                    'lot_code' => $lotCode,
                    'serial_code' => $serialCode,
                    'lpn_id' => $lpnId,
                    'status' => $balanceStatus,
                    'qty' => $qtyAfter,
                    'reserved_qty' => '0',
                ]);
            } else {
                $balance->update(['qty' => $qtyAfter]);
            }

            return StockLedgerEntry::query()->create([
                'tenant_id' => $tenantId,
                'warehouse_id' => $entry['warehouse_id'],
                'product_id' => $entry['product_id'],
                'location_id' => $entry['location_id'],
                'lot_code' => $lotCode,
                'serial_code' => $serialCode,
                'lpn_id' => $lpnId,
                'qty_delta' => $qtyDelta,
                'qty_before' => $qtyBefore,
                'qty_after' => $qtyAfter,
                'type' => $type,
                'reason' => $entry['reason'] ?? null,
                'reference_type' => $entry['reference_type'] ?? null,
                'reference_id' => $entry['reference_id'] ?? null,
                'user_id' => $entry['user_id'] ?? null,
                'device_id' => $entry['device_id'] ?? null,
                'meta' => $entry['meta'] ?? null,
            ]);
        });
    }

    /**
     * @return array{ledger_total: string, balance_total: string, in_sync: bool}
     */
    public function reconcile(int $productId, int $locationId, ?int $warehouseId = null): array
    {
        $ledgerQuery = StockLedgerEntry::query()
            ->where('product_id', $productId)
            ->where('location_id', $locationId);

        $balanceQuery = StockBalance::query()
            ->where('product_id', $productId)
            ->where('location_id', $locationId);

        if ($warehouseId !== null) {
            $ledgerQuery->where('warehouse_id', $warehouseId);
            $balanceQuery->where('warehouse_id', $warehouseId);
        }

        $ledgerTotal = (string) ($ledgerQuery->sum('qty_delta') ?? '0');
        $balanceTotal = (string) ($balanceQuery->sum('qty') ?? '0');

        return [
            'ledger_total' => $ledgerTotal,
            'balance_total' => $balanceTotal,
            'in_sync' => bccomp($ledgerTotal, $balanceTotal, 3) === 0,
        ];
    }

    private function normalizeCode(?string $value): string
    {
        return $value !== null && $value !== '' ? $value : '';
    }

    private function lockBalance(
        int $tenantId,
        int $warehouseId,
        int $productId,
        int $locationId,
        string $lotCode,
        string $serialCode,
        ?int $lpnId,
        StockBalanceStatus $status,
    ): ?StockBalance {
        $query = StockBalance::query()
            ->where('tenant_id', $tenantId)
            ->where('warehouse_id', $warehouseId)
            ->where('product_id', $productId)
            ->where('location_id', $locationId)
            ->where('lot_code', $lotCode)
            ->where('serial_code', $serialCode)
            ->where('status', $status);

        if ($lpnId === null) {
            $query->whereNull('lpn_id');
        } else {
            $query->where('lpn_id', $lpnId);
        }

        return $query->lockForUpdate()->first();
    }

    public function deductForShipment(Allocation $allocation, Model $reference): StockLedgerEntry
    {
        $allocation->loadMissing('salesOrderLine.salesOrder', 'stockBalance');

        $order = $allocation->salesOrderLine->salesOrder;
        $qty = (string) abs((float) $allocation->qty);

        return DB::transaction(function () use ($allocation, $reference, $order, $qty): StockLedgerEntry {
            if ($allocation->stock_balance_id) {
                $balance = StockBalance::query()->whereKey($allocation->stock_balance_id)->lockForUpdate()->first();
                if ($balance !== null) {
                    $balance->reserved_qty = (float) bcsub((string) $balance->reserved_qty, $qty, 3);
                    $balance->save();
                }
            }

            return $this->post([
                'warehouse_id' => (int) $order->warehouse_id,
                'product_id' => (int) $allocation->product_id,
                'location_id' => (int) $allocation->location_id,
                'lot_code' => $allocation->lot_code,
                'qty_delta' => '-'.$qty,
                'type' => StockLedgerType::Ship,
                'reason' => 'outbound_ship',
                'reference_type' => $reference->getMorphClass(),
                'reference_id' => (int) $reference->getKey(),
                'meta' => ['allocation_id' => $allocation->id],
            ]);
        });
    }
}
