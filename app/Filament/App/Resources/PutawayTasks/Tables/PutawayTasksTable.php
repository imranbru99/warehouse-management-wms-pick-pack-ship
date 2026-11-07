<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\PutawayTasks\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PutawayTasksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('status')->badge(),
                TextColumn::make('product.sku')->label('SKU'),
                TextColumn::make('qty'),
                TextColumn::make('fromLocation.code')->label('From'),
                TextColumn::make('toLocation.code')->label('To'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'assigned' => 'Assigned',
                        'in_progress' => 'In progress',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
