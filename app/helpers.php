<?php

use App\Models\Tenant;

if (!function_exists('tenant')) {
    /**
     * Get the current active tenant from the application container.
     */
    function tenant(): ?Tenant
    {
        return app()->bound('tenant') ? app('tenant') : null;
    }
}

if (!function_exists('tenant_id')) {
    /**
     * Get the ID of the current active tenant.
     */
    function tenant_id(): ?int
    {
        return tenant()?->id;
    }
}

