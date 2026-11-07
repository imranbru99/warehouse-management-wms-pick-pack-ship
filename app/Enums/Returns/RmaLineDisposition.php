<?php

declare(strict_types=1);

namespace App\Enums\Returns;

enum RmaLineDisposition: string
{
    case Pending = 'pending';
    case Restock = 'restock';
    case Scrap = 'scrap';
    case Quarantine = 'quarantine';
}
