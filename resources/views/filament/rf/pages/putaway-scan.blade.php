<x-filament-panels::page>
    <div class="mx-auto max-w-lg space-y-4">
        <div class="space-y-2">
            @forelse($this->tasks as $task)
                <button type="button" wire:click="$set('task_id', {{ $task->id }})"
                    class="w-full rounded-lg border px-3 py-3 text-left {{ $task_id === $task->id ? 'border-emerald-500 bg-emerald-50' : '' }}">
                    <div class="font-medium">{{ $task->product?->sku }} · {{ $task->qty }}</div>
                    <div class="text-xs text-gray-500">From {{ $task->fromLocation?->code }}</div>
                </button>
            @empty
                <p class="text-sm text-gray-500">No open putaway tasks.</p>
            @endforelse
        </div>
        <form wire:submit="complete" class="space-y-3">
            <input wire:model="location_code" autofocus autocomplete="off" class="w-full rounded-lg border px-3 py-3 text-lg" placeholder="Scan destination location">
            <button type="submit" class="w-full rounded-lg bg-sky-600 px-4 py-3 text-lg font-semibold text-white">Complete putaway</button>
        </form>
    </div>
</x-filament-panels::page>
