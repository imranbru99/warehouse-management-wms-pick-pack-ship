<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\Asns;

use App\Filament\App\Resources\Asns\Pages\CreateAsn;
use App\Filament\App\Resources\Asns\Pages\EditAsn;
use App\Filament\App\Resources\Asns\Pages\ListAsns;
use App\Filament\App\Resources\Asns\Schemas\AsnForm;
use App\Filament\App\Resources\Asns\Tables\AsnsTable;
use App\Filament\Concerns\HidesWhenModuleDisabled;
use App\Models\Asn;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AsnResource extends Resource
{
    use HidesWhenModuleDisabled;

    protected static string $moduleKey = 'inbound';

    protected static ?string $model = Asn::class;

    protected static ?string $navigationLabel = 'ASNs';

    protected static ?string $modelLabel = 'ASN';

    protected static ?string $pluralModelLabel = 'ASNs';

    protected static ?string $recordTitleAttribute = 'number';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    protected static string|\UnitEnum|null $navigationGroup = 'Inbound';

    protected static ?int $navigationSort = 25;

    public static function form(Schema $schema): Schema
    {
        return AsnForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AsnsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAsns::route('/'),
            'create' => CreateAsn::route('/create'),
            'edit' => EditAsn::route('/{record}/edit'),
        ];
    }
}
