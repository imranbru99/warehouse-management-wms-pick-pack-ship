<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\PurchaseOrders;

use App\Filament\App\Resources\PurchaseOrders\Pages\CreatePurchaseOrder;
use App\Filament\App\Resources\PurchaseOrders\Pages\EditPurchaseOrder;
use App\Filament\App\Resources\PurchaseOrders\Pages\ListPurchaseOrders;
use App\Filament\App\Resources\PurchaseOrders\Schemas\PurchaseOrderForm;
use App\Filament\App\Resources\PurchaseOrders\Tables\PurchaseOrdersTable;
use App\Filament\Concerns\HidesWhenModuleDisabled;
use App\Models\PurchaseOrder;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PurchaseOrderResource extends Resource
{
    use HidesWhenModuleDisabled;

    protected static string $moduleKey = 'inbound';

    protected static ?string $model = PurchaseOrder::class;

    protected static ?string $recordTitleAttribute = 'number';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static string|\UnitEnum|null $navigationGroup = 'Inbound';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return PurchaseOrderForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PurchaseOrdersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPurchaseOrders::route('/'),
            'create' => CreatePurchaseOrder::route('/create'),
            'edit' => EditPurchaseOrder::route('/{record}/edit'),
        ];
    }
}
