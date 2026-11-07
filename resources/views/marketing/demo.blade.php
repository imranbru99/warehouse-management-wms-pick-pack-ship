@extends('layouts.marketing')

@section('title', __('marketing.nav.demo'))
@section('meta_description', 'Demo credentials and panel entry points for the WMS sandbox.')

@section('content')
    <h1 class="mb-6 text-3xl font-semibold">{{ __('marketing.nav.demo') }}</h1>
    <p class="mb-8 text-slate-600 dark:text-slate-300">Run <code class="rounded bg-slate-200 px-1 dark:bg-slate-800">php artisan wms:demo</code> to seed demo tenants and users.</p>
    <div class="grid gap-6 md:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 p-6 dark:border-slate-800">
            <h2 class="font-semibold">{{ __('marketing.nav.login_platform') }}</h2>
            <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">platform@demo.wms.test</p>
            <a href="{{ url('/platform/login') }}" class="mt-4 inline-block text-sm font-medium text-sky-600 dark:text-sky-400">/platform/login</a>
        </div>
        <div class="rounded-2xl border border-slate-200 p-6 dark:border-slate-800">
            <h2 class="font-semibold">{{ __('marketing.nav.login_app') }}</h2>
            <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">owner@demo.wms.test · minimal@demo.wms.test</p>
            <a href="{{ url('/app/login') }}" class="mt-4 inline-block text-sm font-medium text-sky-600 dark:text-sky-400">/app/login</a>
        </div>
    </div>
@endsection
