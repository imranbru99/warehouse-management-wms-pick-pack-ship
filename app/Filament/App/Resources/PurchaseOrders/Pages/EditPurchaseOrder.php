<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\PurchaseOrders\Pages;

use App\Enums\Inbound\PurchaseOrderStatus;
use App\Filament\App\Resources\PurchaseOrders\PurchaseOrderResource;
use App\Models\Location;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderLine;
use App\Services\Inbound\ReceivingService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPurchaseOrder extends EditRecord
{
    protected static string $resource = PurchaseOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('submit')
                ->label('Submit PO')
                ->requiresConfirmation()
                ->visible(fn (): bool => $this->purchaseOrder()->status === PurchaseOrderStatus::Draft)
                ->action(function (): void {
                    $po = $this->purchaseOrder();
                    $po->status = PurchaseOrderStatus::Submitted;
                    $po->save();
                    Notification::make()->title('Purchase order submitted')->success()->send();
                    $this->refreshFormData(['status']);
                }),
            Action::make('receive_all')
                ->label('Receive remaining')
                ->schema([
                    Select::make('location_id')
                        ->label('Dock / receive location')
                        ->options(fn (): array => Location::query()
                            ->where('warehouse_id', $this->purchaseOrder()->warehouse_id)
                            ->whereIn('type', ['receiving', 'staging', 'dock', 'storage'])
                            ->orderBy('code')
                            ->pluck('code', 'id')
                            ->all())
                        ->searchable()
                        ->required(),
                ])
                ->visible(fn (): bool => in_array($this->purchaseOrder()->status, [
                    PurchaseOrderStatus::Submitted,
                    PurchaseOrderStatus::Partial,
                ], true))
                ->action(function (array $data, ReceivingService $receiving): void {
                    $po = $this->purchaseOrder()->load('lines');
                    $location = Location::query()->findOrFail($data['location_id']);

                    $lines = $po->lines
                        ->map(function (PurchaseOrderLine $line): ?array {
                            $remaining = bcsub((string) $line->qty_ordered, (string) $line->qty_received, 3);
                            if (bccomp($remaining, '0', 3) <= 0) {
                                return null;
                            }

                            return [
                                'purchase_order_line_id' => $line->id,
                                'qty' => $remaining,
                            ];
                        })
                        ->filter()
                        ->values()
                        ->all();

                    if ($lines === []) {
                        Notification::make()->title('Nothing left to receive')->warning()->send();

                        return;
                    }

                    $receipt = $receiving->receive($po, $lines, $location, auth()->user());
                    Notification::make()
                        ->title('Received as '.$receipt->number)
                        ->success()
                        ->send();
                    $this->refreshFormData(['status']);
                }),
            DeleteAction::make()
                ->visible(fn (): bool => $this->purchaseOrder()->status === PurchaseOrderStatus::Draft),
        ];
    }

    private function purchaseOrder(): PurchaseOrder
    {
        $record = $this->getRecord();
        if (! $record instanceof PurchaseOrder) {
            throw new \LogicException('Expected a PurchaseOrder record.');
        }

        return $record;
    }
}
