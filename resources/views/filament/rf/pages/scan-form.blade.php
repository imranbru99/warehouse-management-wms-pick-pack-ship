<x-filament-panels::page>
    <form wire:submit="submit" class="space-y-4">
        {{ $this->form }}

        <x-filament::button type="submit" size="lg" class="w-full">
            Confirm scan
        </x-filament::button>
    </form>
</x-filament-panels::page>
