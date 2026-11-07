<?php

declare(strict_types=1);

namespace App\Enums\Inventory;

enum WarehouseTaskState: string
{
    case Pending = 'pending';
    case Assigned = 'assigned';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
}
