<?php

declare(strict_types=1);

namespace App\Enums\Inbound;

enum PurchaseOrderStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';
    case Partial = 'partial';
    case Received = 'received';
    case Cancelled = 'cancelled';
}
