<?php

declare(strict_types=1);

namespace App\Filament\Rf\Pages;

use App\Models\PickTask;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;

class PickScanPage extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHandRaised;

    protected static ?string $navigationLabel = 'Pick';

    protected static ?string $title = 'RF Pick';

    protected static ?int $navigationSort = 30;

    protected string $view = 'filament.rf.pages.pick-scan';

    public ?int $task_id = null;

    public string $location_code = '';

    public string $sku = '';

    /** @return Collection<int, PickTask> */
    public function getTasksProperty(): Collection
    {
        return PickTask::query()
            ->with(['product', 'location', 'salesOrder'])
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->orderBy('pick_sequence')
            ->limit(25)
            ->get();
    }

    public function confirm(): void
    {
        if ($this->task_id === null) {
            Notification::make()->title('Select a pick task')->danger()->send();

            return;
        }

        $task = PickTask::query()->with(['product', 'location'])->findOrFail($this->task_id);
        $locationOk = $task->location !== null && (
            strcasecmp((string) $task->location->code, trim($this->location_code)) === 0
            || strcasecmp((string) $task->location->barcode, trim($this->location_code)) === 0
        );
        $skuOk = $task->product !== null && strcasecmp((string) $task->product->sku, trim($this->sku)) === 0;

        if (! $locationOk || ! $skuOk) {
            Notification::make()->title('Scan mismatch')->body('Location or SKU does not match the task.')->danger()->send();

            return;
        }

        $task->update(['status' => 'completed']);
        Notification::make()->title('Pick confirmed')->success()->send();
        $this->reset('task_id', 'location_code', 'sku');
    }
}
