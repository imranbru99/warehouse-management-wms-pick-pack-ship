<?php

declare(strict_types=1);

namespace App\Enums\Inbound;

enum ReceiptStatus: string
{
    case Open = 'open';
    case Posted = 'posted';
    case Closed = 'closed';
    case Cancelled = 'cancelled';
}
