<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Tenant extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'owner_id',
        'name',
        'slug',
        'domain',
        'description',
        'settings',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Boot the model.
     *
     * Auto-generates the slug from the name if not provided.
     *
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($tenant) {
            if (empty($tenant->slug)) {
                $tenant->slug = Str::slug($tenant->name);
            }
        });
    }

    /**
     * Get the owner (user) that owns the tenant.
     *
     * @return BelongsTo
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the users associated with this tenant.
     *
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role', 'permissions')
            ->withTimestamps();
    }

    /**
     * Check if the given user is the owner of this tenant.
     *
     * @param User $user
     * @return bool
     */
    public function isOwner(User $user): bool
    {
        return $this->owner_id === $user->id;
    }

    /**
     * Check if the given user is associated with this tenant.
     *
     * @param User $user
     * @return bool
     */
    public function hasUser(User $user): bool
    {
        return $this->users()->where('user_id', $user->id)->exists();
    }

    /**
     * Get the role of the given user in this tenant.
     *
     * @param User $user
     * @return string|null Returns 'owner', 'admin', 'member', or null
     */
    public function getUserRole(User $user): ?string
    {
        if ($this->isOwner($user)) {
            return 'owner';
        }

        $pivot = $this->users()->where('user_id', $user->id)->first()?->pivot;
        return $pivot->role ?? null;
    }

    /**
     * Check if the given user is an admin.
     *
     * @param User $user
     * @return bool
     */
    public function isAdmin(User $user): bool
    {
        return $this->getUserRole($user) === 'admin';
    }

    /**
     * Check if the given user can manage users of this tenant.
     *
     * @param User $user
     * @return bool
     */
    public function canManageUsers(User $user): bool
    {
        $role = $this->getUserRole($user);
        return in_array($role, ['owner', 'admin']);
    }

    /**
     * Check if a user has a specific permission for this tenant.
     *
     * @param User $user
     * @param string $permission
     * @return bool
     */
    public function hasPermission(User $user, string $permission): bool
    {
        if ($this->isOwner($user)) {
            return true;
        }

        $pivot = $this->users()->where('user_id', $user->id)->first()?->pivot;

        if (!$pivot) {
            return false;
        }

        $permissions = $pivot->permissions ?? [];

        return in_array('*', $permissions) || in_array($permission, $permissions);
    }

    /**
     * Get the active subscription for this tenant.
     *
     * @return HasOne
     */
    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class)->where('status', 'active')->latest();
    }

    /**
     * Get the current plan for this tenant.
     *
     * @return Plan|null
     */
    public function currentPlan(): ?Plan
    {
        return $this->subscription?->plan;
    }

    /**
     * Check if the tenant can add more users based on plan limits.
     *
     * @return bool
     */
    public function canAddUsers(): bool
    {
        $plan = $this->currentPlan();
        if (!$plan) {
            return false;
        }

        $userLimit = $plan->getLimit('users');
        if ($userLimit === -1) {
            return true;
        }

        $currentUserCount = $this->users()->count();
        return $currentUserCount < $userLimit;
    }

    /**
     * Check if the tenant's current usage is compatible with a new plan.
     *
     * @param Plan $newPlan
     * @return array ['compatible' => bool, 'issues' => array]
     */
    public function isCompatibleWithPlan(Plan $newPlan): array
    {
        $issues = [];

        $newUserLimit = $newPlan->getLimit('users');
        if ($newUserLimit !== -1) {
            $currentUserCount = $this->users()->count();
            if ($currentUserCount > $newUserLimit) {
                $usersToRemove = $currentUserCount - $newUserLimit;
                $issues[] = "Deve remover {$usersToRemove} utilizador(es) antes de mudar para este plano (limite: {$newUserLimit}, atual: {$currentUserCount})";
            }
        }

        return [
            'compatible' => empty($issues),
            'issues' => $issues,
        ];
    }
}
