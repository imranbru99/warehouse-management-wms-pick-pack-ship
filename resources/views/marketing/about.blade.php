@extends('layouts.marketing')

@section('title', __('marketing.nav.about'))
@section('meta_description', 'Multi-tenant WMS by Imran Ahmed — Laravel, Filament, modular warehouse operations.')

@section('content')
    <h1 class="mb-6 text-3xl font-semibold">{{ __('marketing.nav.about') }}</h1>
    <p class="max-w-2xl text-slate-600 dark:text-slate-300">
        {{ config('wms.name') }} is a multi-tenant SaaS warehouse platform built with Laravel 13 and Filament 5.
        Business capabilities ship as toggleable modules synced from <code>modules/*/module.json</code>.
    </p>
    <p class="mt-4 text-slate-600 dark:text-slate-300">
        Built by <a href="https://imrandev.bd" class="text-sky-600 dark:text-sky-400">Imran Ahmed</a> (<a href="mailto:me@imrandev.bd" class="text-sky-600 dark:text-sky-400">me@imrandev.bd</a>).
    </p>
@endsection
