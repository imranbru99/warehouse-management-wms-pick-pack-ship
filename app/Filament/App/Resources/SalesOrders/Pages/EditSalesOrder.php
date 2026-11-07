<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\SalesOrders\Pages;

use App\Enums\SalesOrderStatus;
use App\Filament\App\Resources\SalesOrders\SalesOrderResource;
use App\Models\SalesOrder;
use App\Services\Documents\PickListPdfService;
use App\Services\Outbound\AllocationService;
use App\Services\Outbound\PackService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EditSalesOrder extends EditRecord
{
    protected static string $resource = SalesOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('confirm')
                ->label('Confirm order')
                ->requiresConfirmation()
                ->visible(fn (): bool => $this->salesOrder()->status === SalesOrderStatus::Draft)
                ->action(function (): void {
                    $this->salesOrder()->confirm();
                    Notification::make()->title('Order confirmed')->success()->send();
                    $this->refreshFormData(['status']);
                }),
            Action::make('allocate')
                ->label('Allocate stock')
                ->schema([
                    Select::make('strategy')
                        ->label('Strategy')
                        ->options([
                            'fifo' => 'FIFO',
                            'fefo' => 'FEFO',
                            'nearest' => 'Nearest bin',
                        ])
                        ->default('fifo')
                        ->required(),
                ])
                ->visible(fn (): bool => in_array($this->salesOrder()->status, [
                    SalesOrderStatus::Draft,
                    SalesOrderStatus::Confirmed,
                ], true))
                ->action(function (array $data, AllocationService $allocation): void {
                    $allocation->allocate($this->salesOrder()->fresh('lines'), $data['strategy']);
                    Notification::make()->title('Order allocated')->success()->send();
                    $this->refreshFormData(['status']);
                }),
            Action::make('pack')
                ->label('Pack order')
                ->schema([
                    TextInput::make('length')->numeric()->default(50)->required(),
                    TextInput::make('width')->numeric()->default(50)->required(),
                    TextInput::make('height')->numeric()->default(50)->required(),
                ])
                ->visible(fn (): bool => in_array($this->salesOrder()->status, [
                    SalesOrderStatus::Allocated,
                    SalesOrderStatus::Picking,
                    SalesOrderStatus::Picked,
                ], true))
                ->action(function (array $data, PackService $pack): void {
                    $shipment = $pack->packOrder($this->salesOrder()->fresh('lines'), [
                        'length' => (float) $data['length'],
                        'width' => (float) $data['width'],
                        'height' => (float) $data['height'],
                    ]);
                    Notification::make()
                        ->title('Packed as '.$shipment->number)
                        ->success()
                        ->send();
                    $this->refreshFormData(['status']);
                }),
            Action::make('pick_list_pdf')
                ->label('Pick list PDF')
                ->visible(fn (): bool => in_array($this->salesOrder()->status, [
                    SalesOrderStatus::Allocated,
                    SalesOrderStatus::Picking,
                    SalesOrderStatus::Picked,
                    SalesOrderStatus::Packed,
                    SalesOrderStatus::Shipped,
                ], true))
                ->action(function (PickListPdfService $pdf): StreamedResponse {
                    return $pdf->download($this->salesOrder()->fresh(['lines.product', 'warehouse']));
                }),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    private function salesOrder(): SalesOrder
    {
        $record = $this->getRecord();
        if (! $record instanceof SalesOrder) {
            throw new \LogicException('Expected a SalesOrder record.');
        }

        return $record;
    }
}
