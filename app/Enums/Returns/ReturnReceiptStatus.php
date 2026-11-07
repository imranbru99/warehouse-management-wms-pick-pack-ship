<?php

declare(strict_types=1);

namespace App\Enums\Returns;

enum ReturnReceiptStatus: string
{
    case Draft = 'draft';
    case Posted = 'posted';
}
