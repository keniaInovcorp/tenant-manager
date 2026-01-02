<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * API Controller for tenant management operations.
 *
 * Handles tenant listing and switching operations via API endpoints.
 */
class TenantController extends Controller
{
    /**
     * Get all tenants accessible by the authenticated user.
     *
     * Returns both tenants owned by the user and tenants where the user is a member/admin.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $ownedTenants = $user->ownedTenants()->get();
        $associatedTenants = $user->tenants()->get();

        $allTenants = $ownedTenants->merge($associatedTenants)->unique('id')->values();
        $tenants = $allTenants->map(function ($tenant) {
            return [
                'id' => $tenant->id,
                'name' => $tenant->name,
                'slug' => $tenant->slug,
            ];
        });

        $currentTenant = tenant();
        $current = $currentTenant ? [
            'id' => $currentTenant->id,
            'name' => $currentTenant->name,
            'slug' => $currentTenant->slug,
        ] : null;

        return response()->json([
            'tenants' => $tenants,
            'current' => $current,
        ]);
    }

    /**
     * Switch the active tenant context for the current session.
     *
     * Validates that the user has access to the requested tenant before switching.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function switch(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $tenant = Tenant::findOrFail($request->tenant_id);

        if (!$tenant->hasUser($user) && !$tenant->isOwner($user)) {
            return response()->json(['error' => 'Acesso negado'], 403);
        }

        // Set tenant in session
        session(['tenant_id' => $tenant->id]);

        return response()->json([
            'success' => true,
            'tenant' => [
                'id' => $tenant->id,
                'name' => $tenant->name,
                'slug' => $tenant->slug,
            ],
        ]);
    }
}
