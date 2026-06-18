<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\PlanLimits;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSubscriptionActive
{
    public function __construct(
        protected PlanLimits $planLimits
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->clinic_id) {
            return $next($request);
        }

        $clinic = $user->tenant;

        if (! $clinic) {
            return $next($request);
        }

        if (! $this->planLimits->hasAccess($clinic)) {
            return redirect()->route('filament.app.pages.billing');
        }

        return $next($request);
    }
}
