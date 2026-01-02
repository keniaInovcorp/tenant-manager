<?php

namespace App\Services;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Service for managing tenant creation and lifecycle.
 */
class TenantService
{
    /**
     * Create a new tenant with the given owner and data.
     *
     * @param User $owner
     * @param array $data
     * @return Tenant
     */
    public function createTenant(User $owner, array $data): Tenant
    {
        return DB::transaction(function () use ($owner, $data) {
            $tenant = Tenant::create([
                'owner_id' => $owner->id,
                'name' => $data['name'],
                'slug' => $this->generateUniqueSlug($data['name']),
                'domain' => $data['domain'] ?? null,
                'description' => $data['description'] ?? null,
                'settings' => $data['settings'] ?? [],
                'is_active' => true,
            ]);

            $tenant->users()->attach($owner->id, [
                'role' => 'owner',
                'permissions' => json_encode(['*']),
            ]);

            return $tenant->fresh(['owner', 'users']);
        });
    }

    /**
     * Generate a unique slug from the given name.
     *
     * @param string $name
     * @return string
     */
    protected function generateUniqueSlug(string $name): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        while (Tenant::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
