<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\Waves\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WavesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->searchable()->sortable(),
                TextColumn::make('warehouse.code')->label('Warehouse'),
                TextColumn::make('status')->badge(),
                TextColumn::make('released_at')->dateTime()->toggleable(),
            ])
            ->recordActions([EditAction::make()]);
    }
}
