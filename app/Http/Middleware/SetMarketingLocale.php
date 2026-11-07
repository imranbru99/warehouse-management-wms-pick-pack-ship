<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetMarketingLocale
{
    /** @var list<string> */
    private const SUPPORTED = ['en', 'bn'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = session('locale', config('app.locale', 'en'));

        if ($request->routeIs('locale.switch') && $request->route('locale')) {
            $candidate = (string) $request->route('locale');
            if (in_array($candidate, self::SUPPORTED, true)) {
                session(['locale' => $candidate]);
                $locale = $candidate;
            }
        }

        if (! in_array($locale, self::SUPPORTED, true)) {
            $locale = 'en';
        }

        App::setLocale($locale);

        return $next($request);
    }
}
