<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\SalesOrders\Pages;

use App\Filament\App\Resources\SalesOrders\SalesOrderResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSalesOrder extends CreateRecord
{
    protected static string $resource = SalesOrderResource::class;
}
