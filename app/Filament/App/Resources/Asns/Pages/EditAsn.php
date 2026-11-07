<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\Asns\Pages;

use App\Enums\Inbound\AsnStatus;
use App\Filament\App\Resources\Asns\AsnResource;
use App\Models\Asn;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditAsn extends EditRecord
{
    protected static string $resource = AsnResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('mark_arrived')
                ->label('Mark arrived')
                ->requiresConfirmation()
                ->visible(fn (): bool => in_array($this->asn()->status, [AsnStatus::Expected, AsnStatus::InTransit], true))
                ->action(function (): void {
                    $asn = $this->asn();
                    $asn->status = AsnStatus::Arrived;
                    $asn->arrived_at = now();
                    $asn->save();
                    Notification::make()->title('ASN marked arrived')->success()->send();
                    $this->refreshFormData(['status', 'arrived_at']);
                }),
            DeleteAction::make()
                ->visible(fn (): bool => $this->asn()->status === AsnStatus::Expected),
        ];
    }

    private function asn(): Asn
    {
        $record = $this->getRecord();
        if (! $record instanceof Asn) {
            throw new \LogicException('Expected an Asn record.');
        }

        return $record;
    }
}
