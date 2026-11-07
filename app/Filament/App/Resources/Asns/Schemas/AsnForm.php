<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\Asns\Schemas;

use App\Enums\Inbound\AsnStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AsnForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Advance ship notice')->schema([
                Grid::make(2)->schema([
                    TextInput::make('number')->required()->maxLength(255)->unique(ignoreRecord: true),
                    Select::make('status')
                        ->options(collect(AsnStatus::cases())->mapWithKeys(
                            fn (AsnStatus $s) => [$s->value => str_replace('_', ' ', ucfirst($s->value))]
                        )->all())
                        ->default(AsnStatus::Expected->value)
                        ->required(),
                    Select::make('warehouse_id')->relationship('warehouse', 'name')->searchable()->preload()->required(),
                    Select::make('supplier_id')->relationship('supplier', 'name')->searchable()->preload(),
                    Select::make('purchase_order_id')->relationship('purchaseOrder', 'number')->searchable()->preload(),
                    DateTimePicker::make('expected_at'),
                    DateTimePicker::make('arrived_at'),
                ]),
            ]),
        ]);
    }
}
