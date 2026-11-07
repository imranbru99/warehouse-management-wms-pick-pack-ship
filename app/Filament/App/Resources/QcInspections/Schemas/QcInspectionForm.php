<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\QcInspections\Schemas;

use App\Enums\Inbound\QcInspectionStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class QcInspectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Inspection')->schema([
                Grid::make(2)->schema([
                    Select::make('status')
                        ->options(collect(QcInspectionStatus::cases())->mapWithKeys(
                            fn (QcInspectionStatus $s) => [$s->value => str_replace('_', ' ', ucfirst($s->value))]
                        )->all())
                        ->disabled(),
                    Select::make('product_id')->relationship('product', 'name')->disabled(),
                    TextInput::make('qty_inspected')->numeric(),
                    TextInput::make('qty_failed')->numeric(),
                    Select::make('result')->options([
                        'pass' => 'Pass',
                        'fail' => 'Fail',
                        'quarantine' => 'Quarantine',
                    ]),
                ]),
                Textarea::make('notes')->columnSpanFull(),
            ]),
        ]);
    }
}
