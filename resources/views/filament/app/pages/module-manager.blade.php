<x-filament-panels::page>
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @foreach ($this->modules as $module)
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h3 class="text-base font-semibold text-gray-950 dark:text-white">{{ $module['name'] }}</h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">{{ $module['description'] }}</p>
                    </div>
                    @if ($module['is_core'])
                        <span class="rounded-md bg-teal-100 px-2 py-1 text-xs font-medium text-teal-800">Core</span>
                    @elseif ($module['enabled'])
                        <span class="rounded-md bg-emerald-100 px-2 py-1 text-xs font-medium text-emerald-800">On</span>
                    @else
                        <span class="rounded-md bg-gray-100 px-2 py-1 text-xs font-medium text-gray-700">Off</span>
                    @endif
                </div>

                @if (! empty($module['features']))
                    <div class="mt-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">What you get</p>
                        <ul class="mt-2 list-disc space-y-1 pl-4 text-sm text-gray-700 dark:text-gray-200">
                            @foreach ($module['features'] as $feature)
                                <li>{{ $feature }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (! empty($module['dependencies']))
                    <p class="mt-3 text-xs text-gray-500">
                        Depends on: {{ implode(', ', $module['dependencies']) }}
                    </p>
                @endif

                @if (! empty($module['blocking']))
                    <p class="mt-2 text-xs text-amber-600">
                        Used by: {{ implode(', ', $module['blocking']) }}
                    </p>
                @endif

                <div class="mt-4">
                    <x-filament::button
                        wire:click="toggle('{{ $module['key'] }}')"
                        :disabled="$module['is_core']"
                        color="{{ $module['enabled'] ? 'danger' : 'primary' }}"
                        size="sm"
                    >
                        {{ $module['is_core'] ? 'Always on' : ($module['enabled'] ? 'Turn off' : 'Turn on') }}
                    </x-filament::button>
                </div>
            </div>
        @endforeach
    </div>
</x-filament-panels::page>
