<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\Asns\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AsnsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')->searchable()->sortable(),
                TextColumn::make('status')->badge()->sortable(),
                TextColumn::make('warehouse.name')->sortable(),
                TextColumn::make('supplier.name')->sortable()->toggleable(),
                TextColumn::make('purchaseOrder.number')->label('PO')->toggleable(),
                TextColumn::make('expected_at')->dateTime()->sortable()->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    'expected' => 'Expected',
                    'in_transit' => 'In transit',
                    'arrived' => 'Arrived',
                    'closed' => 'Closed',
                    'cancelled' => 'Cancelled',
                ]),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
