<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\Shipments\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ShipmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')->searchable()->sortable(),
                TextColumn::make('salesOrder.number')->label('Order'),
                TextColumn::make('tracking_number')->searchable()->toggleable(),
                TextColumn::make('carrier')->toggleable(),
                TextColumn::make('status')->badge(),
                TextColumn::make('shipped_at')->dateTime()->toggleable(),
            ])
            ->recordActions([EditAction::make()]);
    }
}
