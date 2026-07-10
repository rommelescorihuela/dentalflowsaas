<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SyncSpatiePermissionsTeamId
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenantId = null;

        if (Filament::getTenant()) {
            $tenantId = Filament::getTenant()->id;
        } elseif (function_exists('tenancy') && tenancy()->tenant) {
            $tenantId = tenancy()->tenant->id;
        }

        if ($tenantId) {
            setPermissionsTeamId($tenantId);

            if ($user = Auth::user()) {
                $user->unsetRelation('roles');
                $user->unsetRelation('permissions');
                $user->forgetCachedPermissions();
            }
        } else {
            setPermissionsTeamId(null);

            if ($user = Auth::user()) {
                $user->unsetRelation('roles');
                $user->unsetRelation('permissions');
                $user->forgetCachedPermissions();
            }
        }

        return $next($request);
    }
}
