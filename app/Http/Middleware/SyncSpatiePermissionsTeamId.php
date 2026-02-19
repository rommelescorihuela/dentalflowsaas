<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Filament\Facades\Filament;

class SyncSpatiePermissionsTeamId
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenantId = null;

        if (Filament::getTenant()) {
            $tenantId = Filament::getTenant()->id;
        }
        elseif (function_exists('tenancy') && tenancy()->tenant) {
            $tenantId = tenancy()->tenant->id;
        }

        if ($tenantId) {
            setPermissionsTeamId($tenantId);

            if ($user = \Illuminate\Support\Facades\Auth::user()) {
                $user->unsetRelation('roles');
                $user->unsetRelation('permissions');
                $user->forgetCachedPermissions();
            }
        }

        return $next($request);
    }
}