<?php

declare(strict_types=1);

namespace App\Services\Outbound\Allocation;

use App\Models\StockBalance;
use Illuminate\Support\Collection;

class NearestBinStrategy implements AllocationStrategy
{
    public function key(): string
    {
        return 'nearest_bin';
    }

    public function sortBalances(Collection $balances): Collection
    {
        return $balances
            ->filter(fn (StockBalance $b) => $b->availableQty() > 0)
            ->sortBy([
                fn (StockBalance $b) => $b->location !== null ? $b->location->pick_sequence : PHP_INT_MAX,
                ['received_at', 'asc'],
            ])
            ->values();
    }
}
