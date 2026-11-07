<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\Waves\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class WaveForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Wave')->schema([
                TextInput::make('code')->required()->maxLength(64),
                Select::make('warehouse_id')->relationship('warehouse', 'name')->required()->searchable()->preload(),
                Select::make('status')->options([
                    'planned' => 'Planned',
                    'released' => 'Released',
                    'picking' => 'Picking',
                    'completed' => 'Completed',
                ])->required(),
                DateTimePicker::make('released_at'),
            ]),
        ]);
    }
}
