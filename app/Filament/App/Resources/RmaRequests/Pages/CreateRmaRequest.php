<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\RmaRequests\Pages;

use App\Filament\App\Resources\RmaRequests\RmaRequestResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRmaRequest extends CreateRecord
{
    protected static string $resource = RmaRequestResource::class;
}
