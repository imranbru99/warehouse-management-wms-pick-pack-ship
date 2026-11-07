<x-filament-panels::page>
    <form wire:submit="submit" class="mx-auto max-w-lg space-y-4">
        <input wire:model="order_number" autofocus autocomplete="off" class="w-full rounded-lg border px-3 py-3 text-lg" placeholder="Scan sales order number">
        <button type="submit" class="w-full rounded-lg bg-indigo-600 px-4 py-3 text-lg font-semibold text-white">Pack with default carton</button>
    </form>
</x-filament-panels::page>
