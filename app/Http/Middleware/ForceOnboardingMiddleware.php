<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceOnboardingMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = tenant();

        // Ensure we are in a tenant context and user is authenticated
        if (!$tenant || !auth()->check()) {
            return $next($request);
        }

        // If onboarding is already completed, proceed
        if ($tenant->onboarding_step >= 4) {
            return $next($request);
        }

        // Prevent infinite loop: Allow access to the onboarding page and Logout
        if (
            $request->routeIs('filament.app.pages.onboarding-wizard') ||
            $request->routeIs('filament.app.auth.logout') ||
            $request->routeIs('livewire.update')
        ) {
            return $next($request);
        }

        return redirect()->route('filament.app.pages.onboarding-wizard');
    }
}
