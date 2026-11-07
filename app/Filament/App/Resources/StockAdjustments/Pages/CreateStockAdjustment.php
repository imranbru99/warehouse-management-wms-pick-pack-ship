<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\StockAdjustments\Pages;

use App\Filament\App\Resources\StockAdjustments\StockAdjustmentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStockAdjustment extends CreateRecord
{
    protected static string $resource = StockAdjustmentResource::class;
}
