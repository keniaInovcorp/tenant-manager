<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetTenantContext
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return $next($request);
        }

        /** @var User $user */
        $user = Auth::user();
        $tenantId = session('tenant_id');

        // If no tenant is set in session, select the first available tenant
        if (!$tenantId) {
            $firstTenant = $user->tenants()->first() ?? $user->ownedTenants()->first();
            if ($firstTenant) {
                session(['tenant_id' => $firstTenant->id]);
                $tenantId = $firstTenant->id;
            }
        }

        if ($tenantId) {
            $tenant = Tenant::find($tenantId);

            // Validate that user has access and tenant is active
            if ($tenant && ($tenant->hasUser($user) || $tenant->isOwner($user)) && $tenant->is_active) {
                app()->instance('tenant', $tenant);
                view()->share('currentTenant', $tenant);
            } else {
                // Invalid tenant, remove from session
                session()->forget('tenant_id');
            }
        }

        return $next($request);
    }
}
