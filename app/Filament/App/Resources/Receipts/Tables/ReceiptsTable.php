<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\Receipts\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ReceiptsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')->searchable()->sortable(),
                TextColumn::make('status')->badge(),
                TextColumn::make('warehouse.name')->sortable(),
                TextColumn::make('purchaseOrder.number')->label('PO')->sortable(),
                TextColumn::make('received_at')->dateTime()->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->defaultSort('received_at', 'desc');
    }
}
