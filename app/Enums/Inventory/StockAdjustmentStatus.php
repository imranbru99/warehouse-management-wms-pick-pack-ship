<?php

declare(strict_types=1);

namespace App\Enums\Inventory;

enum StockAdjustmentStatus: string
{
    case Draft = 'draft';
    case Posted = 'posted';
    case Cancelled = 'cancelled';
}
