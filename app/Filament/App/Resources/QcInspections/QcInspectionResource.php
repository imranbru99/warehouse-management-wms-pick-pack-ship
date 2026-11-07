<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\QcInspections;

use App\Filament\App\Resources\QcInspections\Pages\EditQcInspection;
use App\Filament\App\Resources\QcInspections\Pages\ListQcInspections;
use App\Filament\App\Resources\QcInspections\Schemas\QcInspectionForm;
use App\Filament\App\Resources\QcInspections\Tables\QcInspectionsTable;
use App\Filament\Concerns\HidesWhenModuleDisabled;
use App\Models\QcInspection;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class QcInspectionResource extends Resource
{
    use HidesWhenModuleDisabled;

    protected static string $moduleKey = 'inbound';

    protected static ?string $model = QcInspection::class;

    protected static ?string $navigationLabel = 'QC inspections';

    protected static ?string $modelLabel = 'QC inspection';

    protected static ?string $pluralModelLabel = 'QC inspections';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static string|\UnitEnum|null $navigationGroup = 'Inbound';

    protected static ?int $navigationSort = 35;

    public static function form(Schema $schema): Schema
    {
        return QcInspectionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return QcInspectionsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListQcInspections::route('/'),
            'edit' => EditQcInspection::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
