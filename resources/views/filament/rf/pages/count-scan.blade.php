<x-filament-panels::page>
    <form wire:submit="submit" class="mx-auto max-w-lg space-y-4">
        <input wire:model="location_code" autofocus autocomplete="off" class="w-full rounded-lg border px-3 py-3 text-lg" placeholder="Scan location">
        <input wire:model="sku" autocomplete="off" class="w-full rounded-lg border px-3 py-3 text-lg" placeholder="Scan SKU">
        <input wire:model="counted_qty" type="number" step="0.001" class="w-full rounded-lg border px-3 py-3 text-lg" placeholder="Counted qty">
        <button type="submit" class="w-full rounded-lg bg-slate-700 px-4 py-3 text-lg font-semibold text-white">Save draft count</button>
    </form>
</x-filament-panels::page>
