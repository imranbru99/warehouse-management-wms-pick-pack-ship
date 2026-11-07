<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="@yield('meta_description', config('wms.name'))">
    <meta name="author" content="Imran Ahmed — https://imrandev.bd">
    <title>@yield('title', config('wms.short_name')) — {{ config('wms.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-full bg-slate-50 text-slate-900 antialiased dark:bg-slate-950 dark:text-slate-100">
    <header class="border-b border-slate-200 bg-white/80 backdrop-blur dark:border-slate-800 dark:bg-slate-950/80">
        <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-4 px-4 py-4">
            <a href="{{ route('marketing.home') }}" class="text-lg font-semibold tracking-tight text-slate-900 dark:text-white">
                {{ config('wms.short_name') }}
            </a>
            <nav class="flex flex-wrap items-center gap-4 text-sm font-medium text-slate-600 dark:text-slate-300">
                <a href="{{ route('marketing.home') }}" class="hover:text-slate-900 dark:hover:text-white">{{ __('marketing.nav.home') }}</a>
                <a href="{{ route('marketing.features') }}" class="hover:text-slate-900 dark:hover:text-white">{{ __('marketing.nav.features') }}</a>
                <a href="{{ route('marketing.pricing') }}" class="hover:text-slate-900 dark:hover:text-white">{{ __('marketing.nav.pricing') }}</a>
                <a href="{{ route('marketing.demo') }}" class="hover:text-slate-900 dark:hover:text-white">{{ __('marketing.nav.demo') }}</a>
                <a href="{{ route('marketing.docs') }}" class="hover:text-slate-900 dark:hover:text-white">{{ __('marketing.nav.docs') }}</a>
                <a href="{{ route('marketing.about') }}" class="hover:text-slate-900 dark:hover:text-white">{{ __('marketing.nav.about') }}</a>
                <a href="{{ route('marketing.contact') }}" class="hover:text-slate-900 dark:hover:text-white">{{ __('marketing.nav.contact') }}</a>
            </nav>
            <div class="flex flex-wrap items-center gap-2 text-sm">
                <button type="button" data-theme-toggle class="rounded-lg border border-slate-300 px-3 py-1.5 dark:border-slate-700" aria-label="Toggle theme">
                    <span data-theme-label>{{ __('marketing.theme.dark') }}</span>
                </button>
                <a href="{{ route('locale.switch', 'en') }}" @class(['font-semibold underline' => app()->getLocale() === 'en'])>{{ __('marketing.locale.en') }}</a>
                <span class="text-slate-400">|</span>
                <a href="{{ route('locale.switch', 'bn') }}" @class(['font-semibold underline' => app()->getLocale() === 'bn'])>{{ __('marketing.locale.bn') }}</a>
                <a href="{{ url('/app/login') }}" class="ml-2 rounded-lg bg-slate-900 px-3 py-1.5 text-white dark:bg-slate-100 dark:text-slate-900">{{ __('marketing.nav.login_app') }}</a>
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-6xl px-4 py-10">
        @yield('content')
    </main>

    <footer class="border-t border-slate-200 dark:border-slate-800">
        <div class="mx-auto flex max-w-6xl flex-col gap-2 px-4 py-8 text-sm text-slate-600 dark:text-slate-400 md:flex-row md:items-center md:justify-between">
            <p>{{ __('marketing.footer.tagline') }}</p>
            <div class="flex gap-4">
                <a href="{{ route('marketing.terms') }}" class="hover:underline">{{ __('marketing.footer.terms') }}</a>
                <a href="{{ route('marketing.privacy') }}" class="hover:underline">{{ __('marketing.footer.privacy') }}</a>
                <a href="https://imrandev.bd" class="hover:underline" rel="author">imrandev.bd</a>
            </div>
        </div>
    </footer>
</body>
</html>
