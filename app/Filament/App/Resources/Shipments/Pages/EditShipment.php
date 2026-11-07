<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\Shipments\Pages;

use App\Enums\ShipmentStatus;
use App\Filament\App\Resources\Shipments\ShipmentResource;
use App\Models\Shipment;
use App\Services\Outbound\ShippingService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditShipment extends EditRecord
{
    protected static string $resource = ShipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create_label')
                ->label('Create shipping label')
                ->schema([
                    Select::make('carrier')
                        ->options([
                            'local' => 'Manual / local courier',
                            'generic_rest' => 'Generic REST carrier',
                        ])
                        ->default('local')
                        ->required(),
                ])
                ->visible(fn (): bool => in_array($this->shipment()->status, [
                    ShipmentStatus::Draft,
                    ShipmentStatus::Packed,
                ], true))
                ->action(function (array $data, ShippingService $shipping): void {
                    $shipping->createLabel($this->shipment()->fresh(), $data['carrier']);
                    Notification::make()->title('Label created')->success()->send();
                    $this->refreshFormData(['status', 'carrier', 'tracking_number', 'meta']);
                }),
            Action::make('confirm_ship')
                ->label('Confirm ship')
                ->requiresConfirmation()
                ->visible(fn (): bool => in_array($this->shipment()->status, [
                    ShipmentStatus::Packed,
                    ShipmentStatus::LabelCreated,
                ], true))
                ->action(function (ShippingService $shipping): void {
                    $shipping->confirmShip($this->shipment()->fresh());
                    Notification::make()->title('Shipment confirmed')->success()->send();
                    $this->refreshFormData(['status', 'shipped_at']);
                }),
            DeleteAction::make()
                ->visible(fn (): bool => $this->shipment()->status === ShipmentStatus::Draft),
        ];
    }

    private function shipment(): Shipment
    {
        $record = $this->getRecord();
        if (! $record instanceof Shipment) {
            throw new \LogicException('Expected a Shipment record.');
        }

        return $record;
    }
}
