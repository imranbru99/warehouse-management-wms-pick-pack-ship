<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\PutawayTasks\Pages;

use App\Filament\App\Resources\PutawayTasks\PutawayTaskResource;
use App\Models\PutawayTask;
use App\Services\Inbound\PutawayService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPutawayTask extends EditRecord
{
    protected static string $resource = PutawayTaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('suggest')
                ->label('Suggest location')
                ->visible(fn (): bool => $this->putawayTask()->status->value !== 'completed')
                ->action(function (PutawayService $putaway): void {
                    $putaway->assignSuggested($this->putawayTask());
                    Notification::make()->title('Destination suggested')->success()->send();
                    $this->refreshFormData(['to_location_id', 'status']);
                }),
            Action::make('complete')
                ->label('Complete putaway')
                ->requiresConfirmation()
                ->visible(function (): bool {
                    $task = $this->putawayTask();

                    return $task->to_location_id !== null
                        && $task->status->value !== 'completed';
                })
                ->action(function (PutawayService $putaway): void {
                    $task = $this->putawayTask();
                    $to = $task->toLocation;
                    if ($to === null) {
                        Notification::make()->title('Select a destination location first')->danger()->send();

                        return;
                    }

                    $putaway->complete($task, $to);
                    $this->refreshFormData(['status', 'completed_at']);

                    Notification::make()->title('Putaway completed')->success()->send();
                }),
        ];
    }

    private function putawayTask(): PutawayTask
    {
        $record = $this->getRecord();
        if (! $record instanceof PutawayTask) {
            throw new \LogicException('Expected a PutawayTask record.');
        }

        return $record;
    }
}
