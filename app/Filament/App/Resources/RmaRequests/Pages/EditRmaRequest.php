<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\RmaRequests\Pages;

use App\Enums\Returns\RmaLineDisposition;
use App\Enums\Returns\RmaRequestStatus;
use App\Filament\App\Resources\RmaRequests\RmaRequestResource;
use App\Models\Location;
use App\Models\RmaLine;
use App\Models\RmaRequest;
use App\Services\Returns\ReturnService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditRmaRequest extends EditRecord
{
    protected static string $resource = RmaRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('approve')
                ->label('Approve RMA')
                ->requiresConfirmation()
                ->visible(fn (): bool => in_array($this->rmaRequest()->status, [RmaRequestStatus::Draft, RmaRequestStatus::Submitted], true))
                ->action(function (ReturnService $service): void {
                    $service->approve($this->rmaRequest(), auth()->user());
                    Notification::make()->title('RMA approved')->success()->send();
                    $this->refreshFormData(['status', 'approved_at', 'approved_by']);
                }),
            Action::make('receive_return')
                ->label('Receive return')
                ->schema([
                    Select::make('location_id')
                        ->label('Receive location')
                        ->options(fn (): array => Location::query()
                            ->where('warehouse_id', $this->rmaRequest()->warehouse_id)
                            ->orderBy('code')
                            ->pluck('code', 'id')
                            ->all())
                        ->searchable()
                        ->required(),
                ])
                ->visible(fn (): bool => in_array($this->rmaRequest()->status, [RmaRequestStatus::Approved, RmaRequestStatus::Receiving], true))
                ->action(function (array $data, ReturnService $service): void {
                    $rma = $this->rmaRequest();
                    $location = Location::query()->findOrFail($data['location_id']);
                    $lines = $rma->lines->map(fn (RmaLine $line) => [
                        'rma_line_id' => $line->id,
                        'qty' => bcsub((string) $line->qty_requested, (string) $line->qty_received, 3),
                    ])->filter(fn (array $row) => bccomp($row['qty'], '0', 3) > 0)->values()->all();

                    if ($lines === []) {
                        Notification::make()->title('Nothing left to receive')->warning()->send();

                        return;
                    }

                    $fresh = $rma->fresh();
                    if (! $fresh instanceof RmaRequest) {
                        return;
                    }

                    $service->receive($fresh, $lines, $location, auth()->user());
                    Notification::make()->title('Return received')->success()->send();
                    $this->refreshFormData(['status']);
                }),
            Action::make('restock_line')
                ->label('Restock line')
                ->schema([
                    Select::make('rma_line_id')
                        ->label('Line')
                        ->options(fn (): array => $this->rmaRequest()->lines()
                            ->with('product')
                            ->get()
                            ->mapWithKeys(fn (RmaLine $line) => [
                                $line->id => $line->product->name.' (received: '.$line->qty_received.')',
                            ])
                            ->all())
                        ->required(),
                    Select::make('location_id')
                        ->label('Restock location')
                        ->options(fn (): array => Location::query()
                            ->where('warehouse_id', $this->rmaRequest()->warehouse_id)
                            ->where('type', 'storage')
                            ->orderBy('code')
                            ->pluck('code', 'id')
                            ->all())
                        ->searchable()
                        ->required(),
                ])
                ->visible(fn (): bool => in_array($this->rmaRequest()->status, [RmaRequestStatus::Receiving, RmaRequestStatus::Closed, RmaRequestStatus::Approved], true))
                ->action(function (array $data, ReturnService $service): void {
                    $line = RmaLine::query()->findOrFail($data['rma_line_id']);
                    $location = Location::query()->findOrFail($data['location_id']);
                    $service->applyDisposition($line, RmaLineDisposition::Restock, $location, auth()->user());
                    Notification::make()->title('Line restocked')->success()->send();
                }),
            DeleteAction::make()
                ->visible(fn (): bool => $this->rmaRequest()->status === RmaRequestStatus::Draft),
        ];
    }

    private function rmaRequest(): RmaRequest
    {
        $record = $this->getRecord();
        if (! $record instanceof RmaRequest) {
            throw new \LogicException('Expected RmaRequest record.');
        }

        return $record;
    }
}
