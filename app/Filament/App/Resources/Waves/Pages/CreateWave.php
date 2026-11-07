<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\Waves\Pages;

use App\Filament\App\Resources\Waves\WaveResource;
use Filament\Resources\Pages\CreateRecord;

class CreateWave extends CreateRecord
{
    protected static string $resource = WaveResource::class;
}
