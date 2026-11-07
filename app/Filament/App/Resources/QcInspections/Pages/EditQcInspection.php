<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\QcInspections\Pages;

use App\Enums\Inbound\QcInspectionStatus;
use App\Filament\App\Resources\QcInspections\QcInspectionResource;
use App\Models\QcInspection;
use App\Services\Inbound\QcInspectionService;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditQcInspection extends EditRecord
{
    protected static string $resource = QcInspectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('start')
                ->label('Start inspection')
                ->visible(fn (): bool => $this->inspection()->status === QcInspectionStatus::Pending)
                ->action(function (QcInspectionService $qc): void {
                    $qc->start($this->inspection());
                    Notification::make()->title('Inspection started')->success()->send();
                    $this->refreshFormData(['status']);
                }),
            Action::make('complete')
                ->label('Complete inspection')
                ->schema([
                    TextInput::make('qty_inspected')->numeric()->required()->default(fn () => (float) ($this->inspection()->receiptLine?->qty ?? 1)),
                    TextInput::make('qty_failed')->numeric()->default(0),
                    Select::make('result')->options([
                        'pass' => 'Pass',
                        'fail' => 'Fail',
                        'quarantine' => 'Quarantine',
                    ])->required()->default('pass'),
                    Textarea::make('notes'),
                ])
                ->visible(fn (): bool => in_array($this->inspection()->status, [
                    QcInspectionStatus::Pending,
                    QcInspectionStatus::InProgress,
                ], true))
                ->action(function (array $data, QcInspectionService $qc): void {
                    $qc->complete($this->inspection(), $data, auth()->user());
                    Notification::make()->title('Inspection completed')->success()->send();
                    $this->refreshFormData(['status', 'result', 'qty_inspected', 'qty_failed', 'inspected_at', 'notes']);
                }),
        ];
    }

    private function inspection(): QcInspection
    {
        $record = $this->getRecord();
        if (! $record instanceof QcInspection) {
            throw new \LogicException('Expected a QcInspection record.');
        }

        return $record;
    }
}
