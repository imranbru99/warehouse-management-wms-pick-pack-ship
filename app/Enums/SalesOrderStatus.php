<?php

declare(strict_types=1);

namespace App\Enums;

enum SalesOrderStatus: string
{
    case Draft = 'draft';
    case Confirmed = 'confirmed';
    case Allocated = 'allocated';
    case Picking = 'picking';
    case Picked = 'picked';
    case Packed = 'packed';
    case Shipped = 'shipped';
    case Cancelled = 'cancelled';

    /** @return list<self> */
    public static function ordered(): array
    {
        return [
            self::Draft,
            self::Confirmed,
            self::Allocated,
            self::Picking,
            self::Picked,
            self::Packed,
            self::Shipped,
        ];
    }
}
