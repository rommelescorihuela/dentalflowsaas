<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetTenancyUrlDefaults
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenantId = null;
        if (function_exists('tenant') && tenant('id')) {
            $tenantId = tenant('id');
        } else {
            // Fallback to segment identification for early URL generation (like redirects)
            $segment = $request->segment(1);
            if ($segment && !in_array($segment, ['admin', 'up', 'login', 'register', 'livewire'])) {
                $tenantId = $segment;
            }
        }

        if ($tenantId) {
            URL::defaults(['tenant' => $tenantId]);
        }

        return $next($request);
    }
}
