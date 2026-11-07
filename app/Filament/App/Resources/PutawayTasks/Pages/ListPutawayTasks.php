<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\PutawayTasks\Pages;

use App\Filament\App\Resources\PutawayTasks\PutawayTaskResource;
use Filament\Resources\Pages\ListRecords;

class ListPutawayTasks extends ListRecords
{
    protected static string $resource = PutawayTaskResource::class;
}
