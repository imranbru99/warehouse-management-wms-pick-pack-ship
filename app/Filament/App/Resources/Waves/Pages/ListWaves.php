<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\Waves\Pages;

use App\Filament\App\Resources\Waves\WaveResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListWaves extends ListRecords
{
    protected static string $resource = WaveResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
