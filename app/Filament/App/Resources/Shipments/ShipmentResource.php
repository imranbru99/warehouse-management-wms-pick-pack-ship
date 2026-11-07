<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\Shipments;

use App\Filament\App\Resources\Shipments\Pages\CreateShipment;
use App\Filament\App\Resources\Shipments\Pages\EditShipment;
use App\Filament\App\Resources\Shipments\Pages\ListShipments;
use App\Filament\App\Resources\Shipments\Schemas\ShipmentForm;
use App\Filament\App\Resources\Shipments\Tables\ShipmentsTable;
use App\Filament\Concerns\HidesWhenModuleDisabled;
use App\Models\Shipment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ShipmentResource extends Resource
{
    use HidesWhenModuleDisabled;

    protected static string $moduleKey = 'pack_ship';

    protected static ?string $model = Shipment::class;

    protected static ?string $recordTitleAttribute = 'number';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    protected static string|\UnitEnum|null $navigationGroup = 'Pack & Ship';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return ShipmentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ShipmentsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListShipments::route('/'),
            'create' => CreateShipment::route('/create'),
            'edit' => EditShipment::route('/{record}/edit'),
        ];
    }
}
