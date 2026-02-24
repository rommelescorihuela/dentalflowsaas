<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Stancl\Tenancy\Contracts\TenantCouldNotBeIdentifiedException;
use Stancl\Tenancy\Tenancy;
use Stancl\Tenancy\Resolvers\DomainTenantResolver;
use Symfony\Component\HttpFoundation\Response;

class InitializeTenancyByDomain
{
    /** @var Tenancy */
    protected $tenancy;

    /** @var DomainTenantResolver */
    protected $resolver;

    public function __construct(Tenancy $tenancy, DomainTenantResolver $resolver)
    {
        $this->tenancy = $tenancy;
        $this->resolver = $resolver;
    }

    /**
     * Get host without port.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function getHostWithoutPort(Request $request): string
    {
        $host = $request->getHost();
        
        // If host contains a port, remove it
        if (strpos($host, ':') !== false) {
            return explode(':', $host)[0];
        }
        
        return $host;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();
        $hostWithoutPort = $this->getHostWithoutPort($request);
        
        // Verificar si es un dominio central (con o sin puerto)
        $centralDomains = config('tenancy.central_domains', []);
        
        if (in_array($host, $centralDomains, true) || in_array($hostWithoutPort, $centralDomains, true)) {
            // Es un dominio central, no inicializar tenancy
            return $next($request);
        }

        // No es un dominio central, intentar identificar tenant
        try {
            $this->tenancy->initialize(
                $this->resolver->resolve($hostWithoutPort)
            );
        } catch (\Stancl\Tenancy\Contracts\TenantCouldNotBeIdentifiedException $e) {
            // Si no se puede identificar el tenant, verificar si queremos manejarlo de otra forma
            // Por ahora, lanzamos la excepción
            throw $e;
        }

        return $next($request);
    }
}
