<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\Receipts\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ReceiptForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Receipt')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('number')->disabled(),
                            TextInput::make('status')->disabled(),
                            TextInput::make('warehouse.name')->disabled(),
                            TextInput::make('purchaseOrder.number')->label('PO')->disabled(),
                            TextInput::make('location.code')->label('Dock location')->disabled(),
                            DateTimePicker::make('received_at')->disabled(),
                        ]),
                    ]),
            ]);
    }
}
