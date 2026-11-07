<x-filament-panels::page>
    <div class="grid gap-4 sm:grid-cols-3">
        <a href="{{ \App\Filament\Rf\Pages\ReceiveScanPage::getUrl() }}" class="rounded-xl border border-emerald-200 bg-emerald-50 p-5 dark:border-emerald-800 dark:bg-emerald-950">
            <div class="text-sm font-medium text-emerald-800 dark:text-emerald-200">Receive</div>
            <div class="mt-2 text-3xl font-semibold">{{ $openPos }}</div>
            <div class="text-xs text-emerald-700 dark:text-emerald-300">Open POs</div>
        </a>
        <a href="{{ \App\Filament\Rf\Pages\PutawayScanPage::getUrl() }}" class="rounded-xl border border-sky-200 bg-sky-50 p-5 dark:border-sky-800 dark:bg-sky-950">
            <div class="text-sm font-medium text-sky-800 dark:text-sky-200">Putaway</div>
            <div class="mt-2 text-3xl font-semibold">{{ $openPutaways }}</div>
            <div class="text-xs text-sky-700 dark:text-sky-300">Open tasks</div>
        </a>
        <a href="{{ \App\Filament\Rf\Pages\PickScanPage::getUrl() }}" class="rounded-xl border border-amber-200 bg-amber-50 p-5 dark:border-amber-800 dark:bg-amber-950">
            <div class="text-sm font-medium text-amber-800 dark:text-amber-200">Pick</div>
            <div class="mt-2 text-3xl font-semibold">{{ $openPicks }}</div>
            <div class="text-xs text-amber-700 dark:text-amber-300">Open picks</div>
        </a>
    </div>

    <div class="mt-6 grid gap-3 sm:grid-cols-2">
        <a href="{{ \App\Filament\Rf\Pages\PackScanPage::getUrl() }}" class="rounded-lg border p-4 hover:bg-gray-50 dark:hover:bg-gray-900">Pack order</a>
        <a href="{{ \App\Filament\Rf\Pages\CountScanPage::getUrl() }}" class="rounded-lg border p-4 hover:bg-gray-50 dark:hover:bg-gray-900">Cycle count scan</a>
    </div>
</x-filament-panels::page>
