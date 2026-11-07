<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\Locations;

use App\Filament\App\Resources\Locations\Pages\CreateLocation;
use App\Filament\App\Resources\Locations\Pages\EditLocation;
use App\Filament\App\Resources\Locations\Pages\ListLocations;
use App\Filament\App\Resources\Locations\Schemas\LocationForm;
use App\Filament\App\Resources\Locations\Tables\LocationsTable;
use App\Filament\Concerns\HidesWhenModuleDisabled;
use App\Models\Location;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LocationResource extends Resource
{
    use HidesWhenModuleDisabled;

    protected static string $moduleKey = 'warehouse';

    protected static ?string $model = Location::class;

    protected static ?string $recordTitleAttribute = 'code';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;

    protected static string|\UnitEnum|null $navigationGroup = 'Warehouse';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return LocationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LocationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLocations::route('/'),
            'create' => CreateLocation::route('/create'),
            'edit' => EditLocation::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
