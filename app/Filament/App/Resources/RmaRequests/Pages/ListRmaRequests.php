<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\RmaRequests\Pages;

use App\Filament\App\Resources\RmaRequests\RmaRequestResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRmaRequests extends ListRecords
{
    protected static string $resource = RmaRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
