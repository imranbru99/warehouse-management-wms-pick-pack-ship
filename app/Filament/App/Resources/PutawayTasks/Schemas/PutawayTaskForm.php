<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\PutawayTasks\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PutawayTaskForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Putaway task')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('status')->disabled(),
                            TextInput::make('product.sku')->disabled(),
                            TextInput::make('qty')->disabled(),
                            TextInput::make('fromLocation.code')->label('From')->disabled(),
                            Select::make('to_location_id')
                                ->relationship('toLocation', 'code', fn ($query) => $query->where('type', 'storage'))
                                ->searchable()
                                ->preload()
                                ->label('To location'),
                        ]),
                    ]),
            ]);
    }
}
