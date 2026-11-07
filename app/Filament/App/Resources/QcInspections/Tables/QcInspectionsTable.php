<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\QcInspections\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class QcInspectionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('product.sku')->label('SKU')->searchable(),
                TextColumn::make('status')->badge()->sortable(),
                TextColumn::make('result')->badge()->toggleable(),
                TextColumn::make('qty_inspected')->sortable(),
                TextColumn::make('qty_failed')->sortable(),
                TextColumn::make('inspected_at')->dateTime()->sortable()->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    'pending' => 'Pending',
                    'in_progress' => 'In progress',
                    'completed' => 'Completed',
                    'cancelled' => 'Cancelled',
                ]),
            ])
            ->recordActions([EditAction::make()]);
    }
}
