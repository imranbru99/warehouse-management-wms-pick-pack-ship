<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\StockBalances;

use App\Filament\App\Resources\StockBalances\Pages\ListStockBalances;
use App\Filament\App\Resources\StockBalances\Tables\StockBalancesTable;
use App\Filament\Concerns\HidesWhenModuleDisabled;
use App\Models\StockBalance;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class StockBalanceResource extends Resource
{
    use HidesWhenModuleDisabled;

    protected static string $moduleKey = 'inventory';

    protected static ?string $model = StockBalance::class;

    protected static ?string $navigationLabel = 'On-hand stock';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCircleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Inventory';

    protected static ?int $navigationSort = 10;

    public static function table(Table $table): Table
    {
        return StockBalancesTable::configure($table);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStockBalances::route('/'),
        ];
    }
}
