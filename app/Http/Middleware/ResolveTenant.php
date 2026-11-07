<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Support\Tenancy\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ResolveTenant
{
    public function __construct(protected TenantContext $context) {}

    public function handle(Request $request, Closure $next): Response
    {
        $tenant = $this->resolve($request);

        if ($tenant !== null) {
            $this->context->set($tenant);
            $request->attributes->set('tenant', $tenant);
        }

        return $next($request);
    }

    protected function resolve(Request $request): ?Tenant
    {
        if (! Schema::hasTable('tenants')) {
            return null;
        }

        try {
            $host = $request->getHost();
            $base = config('wms.base_domain', 'wms.test');

            $tenant = Tenant::query()
                ->where('custom_domain', $host)
                ->orWhere(function ($q) use ($host, $base) {
                    if (str_ends_with($host, '.'.$base)) {
                        $sub = substr($host, 0, -strlen('.'.$base));
                        $q->where('subdomain', $sub);
                    }
                })
                ->first();

            if ($tenant !== null) {
                return $tenant;
            }

            if ($request->user()?->tenant_id) {
                return Tenant::query()->find($request->user()->tenant_id);
            }

            $header = $request->header('X-Tenant');
            if ($header) {
                return Tenant::query()
                    ->where('slug', $header)
                    ->orWhere('subdomain', $header)
                    ->orWhere('id', is_numeric($header) ? (int) $header : 0)
                    ->first();
            }
        } catch (Throwable) {
            return null;
        }

        return null;
    }
}
