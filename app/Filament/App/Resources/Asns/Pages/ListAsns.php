<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\Asns\Pages;

use App\Filament\App\Resources\Asns\AsnResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAsns extends ListRecords
{
    protected static string $resource = AsnResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
