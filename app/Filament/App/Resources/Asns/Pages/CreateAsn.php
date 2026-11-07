<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\Asns\Pages;

use App\Filament\App\Resources\Asns\AsnResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAsn extends CreateRecord
{
    protected static string $resource = AsnResource::class;
}
