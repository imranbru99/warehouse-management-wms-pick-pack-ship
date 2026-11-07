<?php

declare(strict_types=1);

namespace App\Services\Outbound\Allocation;

use App\Models\StockBalance;
use Illuminate\Support\Collection;

class FifoStrategy implements AllocationStrategy
{
    public function key(): string
    {
        return 'fifo';
    }

    public function sortBalances(Collection $balances): Collection
    {
        return $balances
            ->filter(fn (StockBalance $b) => $b->availableQty() > 0)
            ->sortBy([
                ['created_at', 'asc'],
                ['id', 'asc'],
            ])
            ->values();
    }
}
