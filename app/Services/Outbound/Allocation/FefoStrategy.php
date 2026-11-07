<?php

declare(strict_types=1);

namespace App\Services\Outbound\Allocation;

use App\Models\StockBalance;
use Illuminate\Support\Collection;

class FefoStrategy implements AllocationStrategy
{
    public function key(): string
    {
        return 'fefo';
    }

    public function sortBalances(Collection $balances): Collection
    {
        return $balances
            ->filter(fn (StockBalance $b) => $b->availableQty() > 0)
            ->sortBy([
                fn (StockBalance $b) => $b->expiry_date !== null ? $b->expiry_date->timestamp : PHP_INT_MAX,
                ['created_at', 'asc'],
                ['id', 'asc'],
            ])
            ->values();
    }
}
