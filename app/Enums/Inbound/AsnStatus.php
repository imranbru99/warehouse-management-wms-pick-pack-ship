<?php

declare(strict_types=1);

namespace App\Enums\Inbound;

enum AsnStatus: string
{
    case Expected = 'expected';
    case InTransit = 'in_transit';
    case Arrived = 'arrived';
    case Closed = 'closed';
    case Cancelled = 'cancelled';
}
