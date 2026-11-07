<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\Warehouses;

use App\Filament\App\Resources\Warehouses\Pages\CreateWarehouse;
use App\Filament\App\Resources\Warehouses\Pages\EditWarehouse;
use App\Filament\App\Resources\Warehouses\Pages\ListWarehouses;
use App\Filament\App\Resources\Warehouses\Schemas\WarehouseForm;
use App\Filament\App\Resources\Warehouses\Tables\WarehousesTable;
use App\Filament\Concerns\HidesWhenModuleDisabled;
use App\Models\Warehouse;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WarehouseResource extends Resource
{
    use HidesWhenModuleDisabled;

    protected static string $moduleKey = 'warehouse';

    protected static ?string $model = Warehouse::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static string|\UnitEnum|null $navigationGroup = 'Warehouse';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return WarehouseForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WarehousesTable::configure($table);
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
            'index' => ListWarehouses::route('/'),
            'create' => CreateWarehouse::route('/create'),
            'edit' => EditWarehouse::route('/{record}/edit'),
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
