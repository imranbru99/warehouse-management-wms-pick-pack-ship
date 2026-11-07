<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\Suppliers\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SupplierForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Supplier')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('code')
                                ->required()
                                ->maxLength(255)
                                ->unique(ignoreRecord: true),
                            TextInput::make('name')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('email')
                                ->email()
                                ->maxLength(255),
                            TextInput::make('phone')
                                ->tel()
                                ->maxLength(255),
                            Select::make('status')
                                ->options([
                                    'active' => 'Active',
                                    'inactive' => 'Inactive',
                                ])
                                ->default('active')
                                ->required(),
                        ]),
                        KeyValue::make('address')
                            ->label('Address')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
