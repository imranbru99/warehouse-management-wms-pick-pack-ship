@extends('layouts.marketing')

@section('title', __('marketing.nav.home'))
@section('meta_description', __('marketing.home.lead'))

@section('content')
    <section class="space-y-6">
        <h1 class="text-4xl font-semibold tracking-tight md:text-5xl">{{ __('marketing.home.title') }}</h1>
        <p class="max-w-2xl text-lg text-slate-600 dark:text-slate-300">{{ __('marketing.home.lead') }}</p>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('marketing.features') }}" class="rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-medium text-white dark:bg-slate-100 dark:text-slate-900">
                {{ __('marketing.home.cta_features') }}
            </a>
            <a href="{{ route('marketing.demo') }}" class="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-medium dark:border-slate-600">
                {{ __('marketing.home.cta_demo') }}
            </a>
        </div>
    </section>
@endsection
