@extends('layouts.marketing')

@section('title', __('marketing.nav.pricing'))
@section('meta_description', 'Modular WMS pricing — enable only the warehouse modules you need.')

@section('content')
    <h1 class="mb-6 text-3xl font-semibold">{{ __('marketing.nav.pricing') }}</h1>
    <p class="mb-8 max-w-2xl text-slate-600 dark:text-slate-300">
        Core warehouse and product modules are included. Add inbound, inventory, outbound, pack &amp; ship, returns, and platform extensions per tenant via Module Manager.
    </p>
    <div class="grid gap-6 md:grid-cols-3">
        @foreach (config('wms.presets') as $key => $preset)
            @if (in_array($key, ['ecommerce', 'minimal', 'small_warehouse'], true))
                <div class="rounded-2xl border border-slate-200 p-6 dark:border-slate-800">
                    <h2 class="text-lg font-semibold">{{ $preset['label'] }}</h2>
                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">{{ count($preset['modules']) }} modules</p>
                    <ul class="mt-4 flex flex-wrap gap-2 text-xs text-slate-500">
                        @foreach ($preset['modules'] as $moduleKey)
                            <li class="rounded-full bg-slate-100 px-2 py-0.5 dark:bg-slate-800">{{ $moduleKey }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        @endforeach
    </div>
@endsection
