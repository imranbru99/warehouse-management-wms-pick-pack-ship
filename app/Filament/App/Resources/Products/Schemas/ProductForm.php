<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\Products\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Product')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('sku')
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
                                    'discontinued' => 'Discontinued',
                                ])
                                ->default('active')
                                ->required(),
                            TextInput::make('brand')->maxLength(255),
                            TextInput::make('category')->maxLength(255),
                            TextInput::make('abc_class')
                                ->label('ABC class')
                                ->maxLength(1),
                        ]),
                        Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
                Section::make('Tracking')
                    ->schema([
                        Grid::make(3)->schema([
                            Toggle::make('track_lot')->label('Track lot'),
                            Toggle::make('track_serial')->label('Track serial'),
                            Toggle::make('track_expiry')->label('Track expiry'),
                        ]),
                        Select::make('shelf_life_strategy')
                            ->options([
                                'FEFO' => 'FEFO',
                                'FIFO' => 'FIFO',
                            ])
                            ->default('FEFO'),
                    ]),
            ]);
    }
}
