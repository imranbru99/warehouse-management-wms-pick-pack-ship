<x-filament-panels::page>
    <form wire:submit="submit" class="mx-auto max-w-lg space-y-4">
        <div>
            <label class="mb-1 block text-sm font-medium">PO number</label>
            <input wire:model="po_number" autofocus autocomplete="off" class="w-full rounded-lg border px-3 py-3 text-lg" placeholder="Scan or type PO">
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium">Dock location</label>
            <select wire:model="location_id" class="w-full rounded-lg border px-3 py-3 text-lg">
                <option value="">Select dock</option>
                @foreach($this->locations as $location)
                    <option value="{{ $location->id }}">{{ $location->code }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="w-full rounded-lg bg-emerald-600 px-4 py-3 text-lg font-semibold text-white">Receive remaining</button>
    </form>
</x-filament-panels::page>
