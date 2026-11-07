@extends('layouts.marketing')

@section('title', __('marketing.nav.contact'))
@section('meta_description', 'Contact Imran Dev BD for WMS demos, integrations, and support.')

@section('content')
    <h1 class="mb-6 text-3xl font-semibold">{{ __('marketing.nav.contact') }}</h1>
    <p class="text-slate-600 dark:text-slate-300">
        Email <a href="mailto:me@imrandev.bd" class="font-medium text-sky-600 dark:text-sky-400">me@imrandev.bd</a>
        or visit <a href="https://imrandev.bd" class="font-medium text-sky-600 dark:text-sky-400">imrandev.bd</a>.
    </p>
@endsection
