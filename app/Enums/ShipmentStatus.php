<?php

declare(strict_types=1);

namespace App\Enums;

enum ShipmentStatus: string
{
    case Draft = 'draft';
    case Packed = 'packed';
    case LabelCreated = 'label_created';
    case Shipped = 'shipped';
    case InTransit = 'in_transit';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';
}
