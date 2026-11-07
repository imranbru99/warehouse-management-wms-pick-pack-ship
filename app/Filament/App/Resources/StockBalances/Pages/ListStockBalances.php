<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\StockBalances\Pages;

use App\Filament\App\Resources\StockBalances\StockBalanceResource;
use Filament\Resources\Pages\ListRecords;

class ListStockBalances extends ListRecords
{
    protected static string $resource = StockBalanceResource::class;
}
