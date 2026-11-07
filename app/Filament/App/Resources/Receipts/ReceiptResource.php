<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\Receipts;

use App\Filament\App\Resources\Receipts\Pages\EditReceipt;
use App\Filament\App\Resources\Receipts\Pages\ListReceipts;
use App\Filament\App\Resources\Receipts\Schemas\ReceiptForm;
use App\Filament\App\Resources\Receipts\Tables\ReceiptsTable;
use App\Filament\Concerns\HidesWhenModuleDisabled;
use App\Models\Receipt;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ReceiptResource extends Resource
{
    use HidesWhenModuleDisabled;

    protected static string $moduleKey = 'inbound';

    protected static ?string $model = Receipt::class;

    protected static ?string $recordTitleAttribute = 'number';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInboxArrowDown;

    protected static string|\UnitEnum|null $navigationGroup = 'Inbound';

    protected static ?int $navigationSort = 30;

    public static function form(Schema $schema): Schema
    {
        return ReceiptForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ReceiptsTable::configure($table);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReceipts::route('/'),
            'edit' => EditReceipt::route('/{record}/edit'),
        ];
    }
}
