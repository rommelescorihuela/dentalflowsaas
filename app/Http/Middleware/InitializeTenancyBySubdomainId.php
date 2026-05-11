<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Stancl\Tenancy\Middleware\IdentificationMiddleware;
use Symfony\Component\HttpFoundation\Response;

class InitializeTenancyBySubdomainId extends IdentificationMiddleware
{
    /** @var callable|null */
    public static $onFail;

    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();

        // Log to see what we are getting
        \Illuminate\Support\Facades\Log::info('TENANCY IDENT ATTEMPT:', [
            'host' => $host,
            'url' => $request->fullUrl(),
        ]);

        $parts = explode('.', $host);
        $subdomain = $parts[0];

        // Hard check for central domains to avoid matching 'localhost' as a tenant
        if (in_array($host, config('tenancy.central_domains', [])) || $subdomain === 'localhost' || $subdomain === '127') {
            return $next($request);
        }

        if ($subdomain) {
            $tenantModel = config('tenancy.tenant_model');
            $tenant = $tenantModel::find($subdomain);

            if ($tenant) {
                tenancy()->initialize($tenant);
                return $next($request);
            }

            \Illuminate\Support\Facades\Log::warning("Tenant not found for subdomain: {$subdomain}");
        }

        return $next($request);
    }
}
