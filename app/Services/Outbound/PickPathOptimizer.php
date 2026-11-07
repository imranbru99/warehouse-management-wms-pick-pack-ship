<?php

declare(strict_types=1);

namespace App\Services\Outbound;

use App\Models\Location;
use App\Models\PickTask;
use Illuminate\Support\Collection;

class PickPathOptimizer
{
    /**
     * Serpentine pick path by aisle and level, breaking ties with pick_sequence.
     *
     * @param  Collection<int, PickTask>  $tasks
     * @return Collection<int, PickTask>
     */
    public function optimizeTasks(Collection $tasks): Collection
    {
        $tasks = $this->withLocationsLoaded($tasks);

        return $tasks
            ->sortBy(fn (PickTask $task) => $this->sortKey($task->location))
            ->values()
            ->each(function (PickTask $task, int $index): void {
                $task->pick_sequence = $index + 1;
            });
    }

    /**
     * @param  Collection<int, Location>  $locations
     * @return Collection<int, Location>
     */
    public function optimizeLocations(Collection $locations): Collection
    {
        return $locations
            ->sortBy(fn (Location $location) => $this->sortKey($location))
            ->values();
    }

    private function sortKey(?Location $location): string
    {
        if ($location === null) {
            return '9999-9999-99999999';
        }

        $aisle = $this->numericComponent($location->aisle);
        $level = $this->numericComponent($location->level);
        $serpentineLevel = ($aisle % 2 === 0) ? $level : (9999 - $level);

        return sprintf('%04d-%04d-%08d', $aisle, $serpentineLevel, $location->pick_sequence);
    }

    private function numericComponent(?string $value): int
    {
        if ($value === null || $value === '') {
            return 0;
        }

        if (preg_match('/\d+/', $value, $matches)) {
            return (int) $matches[0];
        }

        return (int) crc32($value) % 10000;
    }

    /**
     * @param  Collection<int, PickTask>  $tasks
     * @return Collection<int, PickTask>
     */
    private function withLocationsLoaded(Collection $tasks): Collection
    {
        if ($tasks->isEmpty()) {
            return $tasks;
        }

        if ($tasks instanceof \Illuminate\Database\Eloquent\Collection) {
            $tasks->loadMissing('location');

            return $tasks;
        }

        $loaded = PickTask::query()
            ->with('location')
            ->whereIn('id', $tasks->pluck('id'))
            ->get()
            ->keyBy('id');

        return $tasks->map(function (PickTask $task) use ($loaded): PickTask {
            $withLocation = $loaded->get($task->getKey());
            if ($withLocation !== null) {
                $task->setRelation('location', $withLocation->location);
            }

            return $task;
        });
    }
}
