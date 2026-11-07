<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\Shipments\Schemas;

use App\Enums\ShipmentStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ShipmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Shipment')->schema([
                Grid::make(2)->schema([
                    TextInput::make('number')->required()->maxLength(64),
                    Select::make('status')
                        ->options(collect(ShipmentStatus::cases())->mapWithKeys(fn ($c) => [$c->value => ucfirst(str_replace('_', ' ', $c->value))]))
                        ->required(),
                    Select::make('warehouse_id')->relationship('warehouse', 'name')->required()->searchable()->preload(),
                    Select::make('sales_order_id')->relationship('salesOrder', 'number')->required()->searchable()->preload(),
                    TextInput::make('tracking_number')->maxLength(128),
                    TextInput::make('carrier')->maxLength(64),
                    TextInput::make('service')->maxLength(64),
                ]),
            ]),
        ]);
    }
}
