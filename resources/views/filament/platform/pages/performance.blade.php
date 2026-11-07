<x-filament-panels::page>
    <div class="space-y-6">
        <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-900">
            <h2 class="text-lg font-semibold">Drutogoti status</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                Auto-detects N+1 queries, slow SQL, cache misses, and cookie bloat with near-zero request overhead.
            </p>
            <dl class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                <div>
                    <dt class="text-xs uppercase text-gray-500">Enabled</dt>
                    <dd class="text-sm font-medium">{{ $this->status['enabled'] ? 'Yes' : 'No' }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase text-gray-500">Storage</dt>
                    <dd class="text-sm font-medium">{{ $this->status['storage'] }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase text-gray-500">Sample rate</dt>
                    <dd class="text-sm font-medium">{{ $this->status['sample_rate'] }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase text-gray-500">Profiled requests</dt>
                    <dd class="text-sm font-medium">{{ number_format($this->status['requests']) }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase text-gray-500">Suggestions</dt>
                    <dd class="text-sm font-medium">{{ number_format($this->status['suggestions']) }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase text-gray-500">Package</dt>
                    <dd class="text-sm font-medium">{{ $this->status['package'] }}</dd>
                </div>
            </dl>
        </div>
        <p class="text-sm text-gray-600 dark:text-gray-300">
            See <code>docs/PERFORMANCE.md</code> for load-test notes and benchmark methodology.
        </p>
    </div>
</x-filament-panels::page>
