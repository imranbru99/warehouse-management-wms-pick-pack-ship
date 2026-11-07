<?php

declare(strict_types=1);

namespace App\Enums\Inventory;

enum StockBalanceStatus: string
{
    case Available = 'available';
    case Quarantine = 'quarantine';
    case Damaged = 'damaged';
    case Hold = 'hold';
}
