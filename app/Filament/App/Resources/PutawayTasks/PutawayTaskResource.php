<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\PutawayTasks;

use App\Filament\App\Resources\PutawayTasks\Pages\EditPutawayTask;
use App\Filament\App\Resources\PutawayTasks\Pages\ListPutawayTasks;
use App\Filament\App\Resources\PutawayTasks\Schemas\PutawayTaskForm;
use App\Filament\App\Resources\PutawayTasks\Tables\PutawayTasksTable;
use App\Filament\Concerns\HidesWhenModuleDisabled;
use App\Models\PutawayTask;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PutawayTaskResource extends Resource
{
    use HidesWhenModuleDisabled;

    protected static string $moduleKey = 'inbound';

    protected static ?string $model = PutawayTask::class;

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $navigationLabel = 'Putaway tasks';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowRightCircle;

    protected static string|\UnitEnum|null $navigationGroup = 'Inbound';

    protected static ?int $navigationSort = 40;

    public static function form(Schema $schema): Schema
    {
        return PutawayTaskForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PutawayTasksTable::configure($table);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPutawayTasks::route('/'),
            'edit' => EditPutawayTask::route('/{record}/edit'),
        ];
    }
}
