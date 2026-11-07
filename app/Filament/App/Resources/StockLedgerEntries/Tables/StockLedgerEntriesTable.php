<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\StockLedgerEntries\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StockLedgerEntriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')->dateTime()->sortable(),
                TextColumn::make('type')->badge(),
                TextColumn::make('product.sku'),
                TextColumn::make('location.code')->label('Location'),
                TextColumn::make('qty_delta')->label('Delta'),
                TextColumn::make('qty_after')->label('After'),
                TextColumn::make('reason')->toggleable(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
