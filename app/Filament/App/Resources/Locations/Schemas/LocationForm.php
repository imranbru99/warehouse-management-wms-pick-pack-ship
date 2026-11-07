<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\Locations\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LocationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Location')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('warehouse_id')
                                ->relationship('warehouse', 'name')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->live(),
                            Select::make('zone_id')
                                ->relationship(
                                    'zone',
                                    'name',
                                    fn ($query, $get) => $query->when(
                                        $get('warehouse_id'),
                                        fn ($q, $warehouseId) => $q->where('warehouse_id', $warehouseId),
                                    ),
                                )
                                ->searchable()
                                ->preload(),
                            TextInput::make('code')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('barcode')
                                ->maxLength(255),
                            Select::make('type')
                                ->options([
                                    'storage' => 'Storage',
                                    'staging' => 'Staging',
                                    'dock' => 'Dock',
                                    'pick' => 'Pick',
                                ])
                                ->default('storage')
                                ->required(),
                            Select::make('status')
                                ->options([
                                    'active' => 'Active',
                                    'inactive' => 'Inactive',
                                ])
                                ->default('active')
                                ->required(),
                            TextInput::make('aisle')->maxLength(32),
                            TextInput::make('rack')->maxLength(32),
                            TextInput::make('level')->maxLength(32),
                            TextInput::make('bin')->maxLength(32),
                            TextInput::make('pick_sequence')
                                ->numeric()
                                ->default(0),
                            Toggle::make('is_pick_face'),
                            Toggle::make('is_blocked'),
                        ]),
                    ]),
            ]);
    }
}
