<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\SalesOrders\Schemas;

use App\Enums\SalesOrderStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SalesOrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Sales order')->schema([
                Grid::make(2)->schema([
                    TextInput::make('number')->required()->maxLength(64)->unique(ignoreRecord: true),
                    Select::make('status')
                        ->options(collect(SalesOrderStatus::cases())->mapWithKeys(fn ($c) => [$c->value => ucfirst($c->value)]))
                        ->required(),
                    Select::make('warehouse_id')->relationship('warehouse', 'name')->required()->searchable()->preload(),
                    Select::make('customer_id')->relationship('customer', 'name')->searchable()->preload(),
                    TextInput::make('external_ref')->maxLength(128),
                    TextInput::make('priority')->numeric()->default(5)->minValue(1)->maxValue(9),
                    DatePicker::make('required_ship_date'),
                ]),
                Textarea::make('notes')->columnSpanFull(),
            ]),
        ]);
    }
}
