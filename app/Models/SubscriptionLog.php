<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'subscription_id',
        'old_plan_id',
        'new_plan_id',
        'action',
        'old_price',
        'new_price',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'old_price' => 'decimal:2',
        'new_price' => 'decimal:2',
        'metadata' => 'array',
    ];

    /**
     * Get the tenant that owns the log.
     *
     * @return BelongsTo
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the user who performed the action.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the subscription associated with the log.
     *
     * @return BelongsTo
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * Get the old plan.
     *
     * @return BelongsTo
     */
    public function oldPlan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'old_plan_id');
    }

    /**
     * Get the new plan.
     *
     * @return BelongsTo
     */
    public function newPlan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'new_plan_id');
    }

    /**
     * Get formatted action label.
     *
     * @return string
     */
    public function getActionLabelAttribute(): string
    {
        return match($this->action) {
            'subscribed' => 'Subscreveu',
            'upgraded' => 'Upgrade',
            'downgraded' => 'Downgrade',
            'cancelled' => 'Cancelado',
            'renewed' => 'Renovado',
            'trial_started' => 'Trial Iniciado',
            'trial_ended' => 'Trial Terminado',
            default => $this->action,
        };
    }

    /**
     * Get formatted action color class.
     *
     * @return string
     */
    public function getActionColorAttribute(): string
    {
        return match($this->action) {
            'subscribed', 'renewed', 'trial_started' => 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300',
            'upgraded' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300',
            'downgraded' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300',
            'cancelled', 'trial_ended' => 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300',
            default => 'bg-gray-100 dark:bg-gray-900/30 text-gray-800 dark:text-gray-300',
        };
    }
}

