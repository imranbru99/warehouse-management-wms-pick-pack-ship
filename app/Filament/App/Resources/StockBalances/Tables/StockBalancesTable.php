<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\StockBalances\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StockBalancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('warehouse.code')->label('WH'),
                TextColumn::make('product.sku')->searchable(),
                TextColumn::make('location.code')->label('Location'),
                TextColumn::make('status')->badge(),
                TextColumn::make('qty')->sortable(),
                TextColumn::make('reserved_qty')->label('Reserved'),
                TextColumn::make('lot_code')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('warehouse_id')->relationship('warehouse', 'name'),
            ]);
    }
}
