<?php

declare(strict_types=1);

namespace App\Filament\Rf\Pages;

use App\Models\Location;
use App\Models\PutawayTask;
use App\Services\Inbound\PutawayService;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;

class PutawayScanPage extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArchiveBox;

    protected static ?string $navigationLabel = 'Putaway';

    protected static ?string $title = 'RF Putaway';

    protected static ?int $navigationSort = 20;

    protected string $view = 'filament.rf.pages.putaway-scan';

    public ?int $task_id = null;

    public string $location_code = '';

    /** @return Collection<int, PutawayTask> */
    public function getTasksProperty(): Collection
    {
        return PutawayTask::query()
            ->with(['product', 'fromLocation'])
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->latest()
            ->limit(25)
            ->get();
    }

    public function complete(PutawayService $putaway): void
    {
        if ($this->task_id === null) {
            Notification::make()->title('Select a putaway task')->danger()->send();

            return;
        }

        $task = PutawayTask::query()->findOrFail($this->task_id);
        $location = Location::query()
            ->where('warehouse_id', $task->warehouse_id)
            ->where(function ($q): void {
                $q->where('code', trim($this->location_code))
                    ->orWhere('barcode', trim($this->location_code));
            })
            ->first();

        if ($location === null) {
            Notification::make()->title('Location not found')->danger()->send();

            return;
        }

        $putaway->complete($task, $location, auth()->user());
        Notification::make()->title('Putaway completed')->success()->send();
        $this->reset('task_id', 'location_code');
    }
}
