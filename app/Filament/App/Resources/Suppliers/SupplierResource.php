<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\Suppliers;

use App\Filament\App\Resources\Suppliers\Pages\CreateSupplier;
use App\Filament\App\Resources\Suppliers\Pages\EditSupplier;
use App\Filament\App\Resources\Suppliers\Pages\ListSuppliers;
use App\Filament\App\Resources\Suppliers\Schemas\SupplierForm;
use App\Filament\App\Resources\Suppliers\Tables\SuppliersTable;
use App\Filament\Concerns\HidesWhenModuleDisabled;
use App\Models\Supplier;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SupplierResource extends Resource
{
    use HidesWhenModuleDisabled;

    protected static string $moduleKey = 'inbound';

    protected static ?string $model = Supplier::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    protected static string|\UnitEnum|null $navigationGroup = 'Inbound';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return SupplierForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SuppliersTable::configure($table);
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
            'index' => ListSuppliers::route('/'),
            'create' => CreateSupplier::route('/create'),
            'edit' => EditSupplier::route('/{record}/edit'),
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
