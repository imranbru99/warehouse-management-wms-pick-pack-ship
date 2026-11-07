<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\Waves\Pages;

use App\Filament\App\Resources\Waves\WaveResource;
use App\Models\Wave;
use App\Services\Outbound\WaveService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditWave extends EditRecord
{
    protected static string $resource = WaveResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('release')
                ->label('Release wave')
                ->requiresConfirmation()
                ->visible(fn (): bool => $this->wave()->status === 'planned')
                ->action(function (WaveService $waves): void {
                    $waves->release($this->wave());
                    Notification::make()->title('Wave released')->success()->send();
                    $this->refreshFormData(['status', 'released_at']);
                }),
            DeleteAction::make()
                ->visible(fn (): bool => $this->wave()->status === 'planned'),
        ];
    }

    private function wave(): Wave
    {
        $record = $this->getRecord();
        if (! $record instanceof Wave) {
            throw new \LogicException('Expected a Wave record.');
        }

        return $record;
    }
}
