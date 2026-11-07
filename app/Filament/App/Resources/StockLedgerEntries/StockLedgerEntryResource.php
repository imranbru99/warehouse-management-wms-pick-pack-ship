<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\StockLedgerEntries;

use App\Filament\App\Resources\StockLedgerEntries\Pages\ListStockLedgerEntries;
use App\Filament\App\Resources\StockLedgerEntries\Tables\StockLedgerEntriesTable;
use App\Filament\Concerns\HidesWhenModuleDisabled;
use App\Models\StockLedgerEntry;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class StockLedgerEntryResource extends Resource
{
    use HidesWhenModuleDisabled;

    protected static string $moduleKey = 'inventory';

    protected static ?string $model = StockLedgerEntry::class;

    protected static ?string $navigationLabel = 'Stock ledger';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static string|\UnitEnum|null $navigationGroup = 'Inventory';

    protected static ?int $navigationSort = 30;

    public static function table(Table $table): Table
    {
        return StockLedgerEntriesTable::configure($table);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStockLedgerEntries::route('/'),
        ];
    }
}
