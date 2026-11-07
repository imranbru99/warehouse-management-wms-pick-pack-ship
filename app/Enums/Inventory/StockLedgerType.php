<?php

declare(strict_types=1);

namespace App\Enums\Inventory;

enum StockLedgerType: string
{
    case Receive = 'receive';
    case Putaway = 'putaway';
    case Move = 'move';
    case Adjust = 'adjust';
    case Pick = 'pick';
    case Pack = 'pack';
    case Ship = 'ship';
    case Count = 'count';
    case Replenish = 'replenish';
    case Return = 'return';
    case TransferOut = 'transfer_out';
    case TransferIn = 'transfer_in';
    case KitConsume = 'kit_consume';
    case KitBuild = 'kit_build';
}
