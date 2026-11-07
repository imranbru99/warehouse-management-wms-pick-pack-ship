<?php

declare(strict_types=1);

namespace App\Services\Outbound\Allocation;

use App\Models\StockBalance;
use Illuminate\Support\Collection;

interface AllocationStrategy
{
    public function key(): string;

    /**
     * @param  Collection<int, StockBalance>  $balances
     * @return Collection<int, StockBalance>
     */
    public function sortBalances(Collection $balances): Collection;
}
