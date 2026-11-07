<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\QcInspections\Pages;

use App\Filament\App\Resources\QcInspections\QcInspectionResource;
use Filament\Resources\Pages\ListRecords;

class ListQcInspections extends ListRecords
{
    protected static string $resource = QcInspectionResource::class;
}
