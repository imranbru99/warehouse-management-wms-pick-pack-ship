<?php

declare(strict_types=1);

namespace App\Enums\Returns;

enum RmaRequestStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';
    case Approved = 'approved';
    case Receiving = 'receiving';
    case Closed = 'closed';
    case Cancelled = 'cancelled';
}
