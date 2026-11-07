@extends('layouts.marketing')

@section('title', $document === 'privacy' ? __('marketing.footer.privacy') : __('marketing.footer.terms'))
@section('meta_description', 'Legal information for the WMS platform.')

@section('content')
    <h1 class="mb-6 text-3xl font-semibold">
        {{ $document === 'privacy' ? __('marketing.footer.privacy') : __('marketing.footer.terms') }}
    </h1>
    <div class="prose prose-slate max-w-none dark:prose-invert">
        @if ($document === 'privacy')
            <p>We process account and warehouse operational data to provide the service. Demo data is synthetic. Contact us for data requests at me@imrandev.bd.</p>
        @else
            <p>Use of this software is subject to the MIT license bundled with the repository. Hosted SaaS terms may apply for production deployments.</p>
        @endif
    </div>
@endsection
