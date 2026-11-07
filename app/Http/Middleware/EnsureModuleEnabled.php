<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Support\Modules\Module;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureModuleEnabled
{
    public function handle(Request $request, Closure $next, string $module): Response
    {
        if (! Module::enabled($module)) {
            abort(403, "Module [{$module}] is not enabled for this tenant.");
        }

        return $next($request);
    }
}
