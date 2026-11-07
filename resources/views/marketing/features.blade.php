@extends('layouts.marketing')

@section('title', __('marketing.nav.features'))
@section('meta_description', 'Toggleable WMS modules per tenant — inbound, inventory, outbound, returns, and more.')

@section('content')
    <h1 class="mb-8 text-3xl font-semibold">{{ __('marketing.nav.features') }}</h1>
    <div class="grid gap-6 md:grid-cols-2">
        @foreach ($modules as $module)
            <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <h2 class="text-xl font-semibold">{{ $module->name }}</h2>
                <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">{{ $module->description }}</p>
                @if (! empty($module->features))
                    <ul class="mt-4 list-disc space-y-1 pl-5 text-sm text-slate-700 dark:text-slate-300">
                        @foreach ($module->features as $feature)
                            <li>{{ $feature }}</li>
                        @endforeach
                    </ul>
                @endif
            </article>
        @endforeach
    </div>
@endsection
