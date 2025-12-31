<?php

namespace App\Policies;

use App\Models\Tenant;
use App\Models\User;

/**
 * Policy for tenant authorization.
 *
 * Defines which users can perform various actions on tenants.
 */
class TenantPolicy
{
    /**
     * Determine whether the user can view any tenants.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the tenant.
     *
     * @param User $user
     * @param Tenant $tenant
     * @return bool
     */
    public function view(User $user, Tenant $tenant): bool
    {
        return $tenant->owner_id === $user->id;
    }

    /**
     * Determine whether the user can create tenants.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the tenant.
     *
     * @param User $user
     * @param Tenant $tenant
     * @return bool
     */
    public function update(User $user, Tenant $tenant): bool
    {
        return $tenant->owner_id === $user->id;
    }
}
