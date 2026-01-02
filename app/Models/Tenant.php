<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
}
