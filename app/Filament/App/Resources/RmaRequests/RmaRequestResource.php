<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\RmaRequests;

use App\Filament\App\Resources\RmaRequests\Pages\CreateRmaRequest;
use App\Filament\App\Resources\RmaRequests\Pages\EditRmaRequest;
use App\Filament\App\Resources\RmaRequests\Pages\ListRmaRequests;
use App\Filament\App\Resources\RmaRequests\Schemas\RmaRequestForm;
use App\Filament\App\Resources\RmaRequests\Tables\RmaRequestsTable;
use App\Filament\Concerns\HidesWhenModuleDisabled;
use App\Models\RmaRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RmaRequestResource extends Resource
{
    use HidesWhenModuleDisabled;

    protected static string $moduleKey = 'returns';

    protected static ?string $model = RmaRequest::class;

    protected static ?string $navigationLabel = 'RMA requests';

    protected static ?string $modelLabel = 'RMA request';

    protected static ?string $pluralModelLabel = 'RMA requests';

    protected static ?string $recordTitleAttribute = 'number';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowUturnLeft;

    protected static string|\UnitEnum|null $navigationGroup = 'Returns';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return RmaRequestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RmaRequestsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRmaRequests::route('/'),
            'create' => CreateRmaRequest::route('/create'),
            'edit' => EditRmaRequest::route('/{record}/edit'),
        ];
    }
}
