<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\StockAdjustments;

use App\Filament\App\Resources\StockAdjustments\Pages\CreateStockAdjustment;
use App\Filament\App\Resources\StockAdjustments\Pages\EditStockAdjustment;
use App\Filament\App\Resources\StockAdjustments\Pages\ListStockAdjustments;
use App\Filament\App\Resources\StockAdjustments\Schemas\StockAdjustmentForm;
use App\Filament\App\Resources\StockAdjustments\Tables\StockAdjustmentsTable;
use App\Filament\Concerns\HidesWhenModuleDisabled;
use App\Models\StockAdjustment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class StockAdjustmentResource extends Resource
{
    use HidesWhenModuleDisabled;

    protected static string $moduleKey = 'inventory';

    protected static ?string $model = StockAdjustment::class;

    protected static ?string $recordTitleAttribute = 'number';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAdjustmentsHorizontal;

    protected static string|\UnitEnum|null $navigationGroup = 'Inventory';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return StockAdjustmentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StockAdjustmentsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStockAdjustments::route('/'),
            'create' => CreateStockAdjustment::route('/create'),
            'edit' => EditStockAdjustment::route('/{record}/edit'),
        ];
    }
}
