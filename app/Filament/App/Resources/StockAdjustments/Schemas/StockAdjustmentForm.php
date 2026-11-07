<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\StockAdjustments\Schemas;

use App\Enums\Inventory\StockAdjustmentStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StockAdjustmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Stock adjustment')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('number')
                                ->required()
                                ->maxLength(255)
                                ->unique(ignoreRecord: true),
                            Select::make('status')
                                ->options(collect(StockAdjustmentStatus::cases())->mapWithKeys(
                                    fn (StockAdjustmentStatus $s) => [$s->value => ucfirst($s->value)]
                                )->all())
                                ->default(StockAdjustmentStatus::Draft->value)
                                ->disabled(fn (?string $operation): bool => $operation === 'edit')
                                ->dehydrated(),
                            Select::make('warehouse_id')
                                ->relationship('warehouse', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),
                            TextInput::make('reason')->maxLength(255),
                        ]),
                        Textarea::make('notes')->columnSpanFull(),
                    ]),
            ]);
    }
}
