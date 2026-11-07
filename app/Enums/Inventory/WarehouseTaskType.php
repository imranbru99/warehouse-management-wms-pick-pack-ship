<?php

declare(strict_types=1);

namespace App\Enums\Inventory;

enum WarehouseTaskType: string
{
    case Receive = 'receive';
    case Putaway = 'putaway';
    case Pick = 'pick';
    case Pack = 'pack';
    case Replenish = 'replenish';
    case Move = 'move';
    case Count = 'count';
    case Load = 'load';
}
