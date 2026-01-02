<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToTenant
{
    /**
     * Boot the trait and register model events and global scopes.
     */
    protected static function bootBelongsToTenant(): void
    {
        // Automatically set tenant_id when creating a new record
        static::creating(function ($model) {
            if (tenant_id() && !$model->tenant_id) {
                $model->tenant_id = tenant_id();
            }
        });

        static::addGlobalScope('tenant', function (Builder $builder) {
            if (tenant_id()) {
                $builder->where('tenant_id', tenant_id());
            }
        });
    }

    /**
     * Scope a query to a specific tenant.
     *
     * @param Builder $query
     * @param int|null $tenantId
     * @return Builder
     */
    public function scopeForTenant(Builder $query, ?int $tenantId = null): Builder
    {
        return $query->where('tenant_id', $tenantId ?? tenant_id());
    }
}
