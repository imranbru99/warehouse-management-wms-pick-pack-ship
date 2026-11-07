<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\Shipments\Pages;

use App\Filament\App\Resources\Shipments\ShipmentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateShipment extends CreateRecord
{
    protected static string $resource = ShipmentResource::class;
}
