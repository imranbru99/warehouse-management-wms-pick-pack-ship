<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\PurchaseOrders\Schemas;

use App\Enums\Inbound\PurchaseOrderStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PurchaseOrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Purchase order')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('number')
                                ->required()
                                ->maxLength(255)
                                ->unique(ignoreRecord: true),
                            Select::make('status')
                                ->options(collect(PurchaseOrderStatus::cases())->mapWithKeys(
                                    fn (PurchaseOrderStatus $s) => [$s->value => str_replace('_', ' ', ucfirst($s->value))]
                                )->all())
                                ->default(PurchaseOrderStatus::Draft->value)
                                ->required(),
                            Select::make('warehouse_id')
                                ->relationship('warehouse', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),
                            Select::make('supplier_id')
                                ->relationship('supplier', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),
                            DateTimePicker::make('expected_at'),
                        ]),
                        Textarea::make('notes')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
