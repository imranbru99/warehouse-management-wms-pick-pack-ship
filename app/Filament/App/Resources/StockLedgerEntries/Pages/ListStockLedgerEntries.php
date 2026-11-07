<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\StockLedgerEntries\Pages;

use App\Filament\App\Resources\StockLedgerEntries\StockLedgerEntryResource;
use Filament\Resources\Pages\ListRecords;

class ListStockLedgerEntries extends ListRecords
{
    protected static string $resource = StockLedgerEntryResource::class;
}
