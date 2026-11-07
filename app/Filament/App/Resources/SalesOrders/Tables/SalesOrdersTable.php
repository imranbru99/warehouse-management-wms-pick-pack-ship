<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\SalesOrders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class SalesOrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')->searchable()->sortable(),
                TextColumn::make('customer.name')->toggleable(),
                TextColumn::make('warehouse.code')->label('Warehouse'),
                TextColumn::make('status')->badge()->sortable(),
                TextColumn::make('ordered_at')->dateTime()->toggleable(),
                TextColumn::make('required_ship_date')->date()->toggleable(),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('status')->options([
                    'draft' => 'Draft',
                    'confirmed' => 'Confirmed',
                    'allocated' => 'Allocated',
                    'shipped' => 'Shipped',
                ]),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
