<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\Warehouses\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class WarehouseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Warehouse')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('code')
                                ->required()
                                ->maxLength(255)
                                ->unique(ignoreRecord: true),
                            TextInput::make('name')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('slug')
                                ->maxLength(255)
                                ->helperText('Leave blank to generate from name.'),
                            Select::make('status')
                                ->options([
                                    'active' => 'Active',
                                    'inactive' => 'Inactive',
                                ])
                                ->default('active')
                                ->required(),
                            TextInput::make('timezone')
                                ->default('UTC')
                                ->required()
                                ->maxLength(255),
                            Toggle::make('is_default')
                                ->label('Default warehouse'),
                        ]),
                        KeyValue::make('address')
                            ->label('Address')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
