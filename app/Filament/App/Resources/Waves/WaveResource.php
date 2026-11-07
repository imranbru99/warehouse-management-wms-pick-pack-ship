<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\Waves;

use App\Filament\App\Resources\Waves\Pages\CreateWave;
use App\Filament\App\Resources\Waves\Pages\EditWave;
use App\Filament\App\Resources\Waves\Pages\ListWaves;
use App\Filament\App\Resources\Waves\Schemas\WaveForm;
use App\Filament\App\Resources\Waves\Tables\WavesTable;
use App\Filament\Concerns\HidesWhenModuleDisabled;
use App\Models\Wave;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class WaveResource extends Resource
{
    use HidesWhenModuleDisabled;

    protected static string $moduleKey = 'outbound';

    protected static ?string $model = Wave::class;

    protected static ?string $recordTitleAttribute = 'code';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQueueList;

    protected static string|\UnitEnum|null $navigationGroup = 'Outbound';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return WaveForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WavesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWaves::route('/'),
            'create' => CreateWave::route('/create'),
            'edit' => EditWave::route('/{record}/edit'),
        ];
    }
}
