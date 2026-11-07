<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\SalesOrders;

use App\Filament\App\Resources\SalesOrders\Pages\CreateSalesOrder;
use App\Filament\App\Resources\SalesOrders\Pages\EditSalesOrder;
use App\Filament\App\Resources\SalesOrders\Pages\ListSalesOrders;
use App\Filament\App\Resources\SalesOrders\Schemas\SalesOrderForm;
use App\Filament\App\Resources\SalesOrders\Tables\SalesOrdersTable;
use App\Filament\Concerns\HidesWhenModuleDisabled;
use App\Models\SalesOrder;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SalesOrderResource extends Resource
{
    use HidesWhenModuleDisabled;

    protected static string $moduleKey = 'outbound';

    protected static ?string $model = SalesOrder::class;

    protected static ?string $recordTitleAttribute = 'number';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingCart;

    protected static string|\UnitEnum|null $navigationGroup = 'Outbound';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return SalesOrderForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SalesOrdersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSalesOrders::route('/'),
            'create' => CreateSalesOrder::route('/create'),
            'edit' => EditSalesOrder::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class]);
    }
}
