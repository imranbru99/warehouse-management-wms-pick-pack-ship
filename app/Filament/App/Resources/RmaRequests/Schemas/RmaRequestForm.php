<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\RmaRequests\Schemas;

use App\Enums\Returns\RmaLineDisposition;
use App\Enums\Returns\RmaRequestStatus;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RmaRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('RMA')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('number')
                                ->required()
                                ->maxLength(255)
                                ->unique(ignoreRecord: true),
                            Select::make('status')
                                ->options(collect(RmaRequestStatus::cases())->mapWithKeys(
                                    fn (RmaRequestStatus $s) => [$s->value => str_replace('_', ' ', ucfirst($s->value))]
                                )->all())
                                ->default(RmaRequestStatus::Draft->value)
                                ->required()
                                ->disabledOn('edit'),
                            Select::make('warehouse_id')
                                ->relationship('warehouse', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),
                            Select::make('customer_id')
                                ->relationship('customer', 'name')
                                ->searchable()
                                ->preload(),
                            Select::make('sales_order_id')
                                ->relationship('salesOrder', 'number')
                                ->searchable()
                                ->preload(),
                            TextInput::make('reason')
                                ->maxLength(255),
                        ]),
                        Textarea::make('notes')
                            ->columnSpanFull(),
                    ]),
                Section::make('Lines')
                    ->schema([
                        Repeater::make('lines')
                            ->relationship()
                            ->schema([
                                Select::make('product_id')
                                    ->relationship('product', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                TextInput::make('qty_requested')
                                    ->numeric()
                                    ->required()
                                    ->default(1),
                                Select::make('disposition')
                                    ->options(collect(RmaLineDisposition::cases())->mapWithKeys(
                                        fn (RmaLineDisposition $d) => [$d->value => ucfirst($d->value)]
                                    )->all())
                                    ->default(RmaLineDisposition::Pending->value)
                                    ->disabledOn('create'),
                                Textarea::make('condition_notes')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->defaultItems(1)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
