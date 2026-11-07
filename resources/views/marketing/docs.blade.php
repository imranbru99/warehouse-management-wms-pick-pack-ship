@extends('layouts.marketing')

@section('title', __('marketing.nav.docs'))
@section('meta_description', 'WMS documentation stub — architecture, modules, and operations guides.')

@section('content')
    <h1 class="mb-6 text-3xl font-semibold">{{ __('marketing.nav.docs') }}</h1>
    <ul class="list-disc space-y-2 pl-5 text-slate-700 dark:text-slate-300">
        <li><code>docs/PLAN.md</code> — architecture and phases</li>
        <li><code>docs/PROGRESS.md</code> — delivery status</li>
        <li><code>docs/PACKAGES.md</code> — imrandevbd packages</li>
        <li><code>docs/PERFORMANCE.md</code> — Drutogoti and load notes</li>
    </ul>
@endsection
