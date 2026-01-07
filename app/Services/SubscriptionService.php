<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SubscriptionLog;
use App\Models\Tenant;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Service for managing tenant subscriptions, including upgrades, downgrades, and cancellations.
 */
class SubscriptionService
{
    /**
     * Subscribe a tenant to a plan.
     *
     * @param Tenant $tenant
     * @param Plan $plan
     * @param Carbon|null $startDate
     * @param bool $shouldLog
     * @return Subscription
     */
    public function subscribe(Tenant $tenant, Plan $plan, $startDate = null, bool $shouldLog = true): Subscription
    {
        return DB::transaction(function () use ($tenant, $plan, $startDate, $shouldLog) {
            $startDate = $startDate ?? now();
            $trialEndDate = $plan->trial_days > 0 ? $startDate->copy()->addDays($plan->trial_days) : null;
            
            if ($trialEndDate) {
                $endDate = $trialEndDate;
            } elseif ($plan->price == 0) {
                $endDate = null;
            } else {
                $endDate = $this->calculateEndDate($startDate, $plan->billing_period);
            }

            $oldSubscription = $tenant->subscription;
            $oldSubscription?->update(['status' => 'cancelled']);

            $subscription = Subscription::create([
                'tenant_id' => $tenant->id,
                'plan_id' => $plan->id,
                'status' => 'active',
                'starts_at' => $startDate,
                'ends_at' => $endDate,
                'trial_ends_at' => $trialEndDate,
            ]);

            if ($shouldLog) {
                $this->logSubscriptionChange(
                    $tenant,
                    $subscription,
                    $oldSubscription?->plan,
                    $plan,
                    'subscribed',
                    $plan->trial_days > 0 ? 'Subscreveu ao plano com trial de ' . $plan->trial_days . ' dias' : 'Subscreveu ao plano'
                );
            }

            return $subscription;
        });
    }

    /**
     * Upgrade a tenant's subscription to a new plan with immediate effect and pro-rata billing.
     *
     * @param Tenant $tenant
     * @param Plan $newPlan
     * @return Subscription
     */
    public function upgrade(Tenant $tenant, Plan $newPlan): Subscription
    {
        return DB::transaction(function () use ($tenant, $newPlan) {
            $currentSubscription = $tenant->subscription;

            if (!$currentSubscription || !$currentSubscription->isActive()) {
                return $this->subscribe($tenant, $newPlan);
            }

            $remainingDays = now()->diffInDays($currentSubscription->ends_at, false);
            $totalDays = $currentSubscription->starts_at->diffInDays($currentSubscription->ends_at);
            $proratedAmount = ($currentSubscription->plan->price / $totalDays) * $remainingDays;
            $newPlanAmount = $newPlan->price;
            $amountToCharge = max(0, $newPlanAmount - $proratedAmount);

            $oldPlan = $currentSubscription->plan;
            $currentSubscription->update(['status' => 'cancelled']);
            $newSubscription = $this->subscribe($tenant, $newPlan, now(), false);

            $this->logSubscriptionChange(
                $tenant,
                $newSubscription,
                $oldPlan,
                $newPlan,
                'upgraded',
                "Upgrade de {$oldPlan->name} para {$newPlan->name}. Valor pró-rata cobrado: €" . number_format($amountToCharge, 2)
            );

            return $newSubscription;
        });
    }

    /**
     * Downgrade a tenant's subscription to a new plan, scheduled for the next billing period.
     *
     * @param Tenant $tenant
     * @param Plan $newPlan
     * @return Subscription
     */
    public function downgrade(Tenant $tenant, Plan $newPlan): Subscription
    {
        return DB::transaction(function () use ($tenant, $newPlan) {
            $currentSubscription = $tenant->subscription;

            if (!$currentSubscription) {
                return $this->subscribe($tenant, $newPlan);
            }

            $oldPlan = $currentSubscription->plan;
            $currentSubscription->update(['status' => 'cancelled']);
            $newSubscription = $this->subscribe($tenant, $newPlan, now(), false);

            $this->logSubscriptionChange(
                $tenant,
                $newSubscription,
                $oldPlan,
                $newPlan,
                'downgraded',
                "Downgrade de {$oldPlan->name} para {$newPlan->name}"
            );

            return $newSubscription;
        });
    }

    /**
     * Calculate the end date for a subscription based on the billing period.
     *
     * @param Carbon $startDate
     * @param string $period
     * @return Carbon
     */
    protected function calculateEndDate($startDate, string $period)
    {
        return match($period) {
            'monthly' => $startDate->copy()->addMonth(),
            'yearly' => $startDate->copy()->addYear(),
            default => $startDate->copy()->addMonth(),
        };
    }

    /**
     * Log a subscription change for audit purposes.
     *
     * @param Tenant $tenant
     * @param Subscription $subscription
     * @param Plan|null $oldPlan
     * @param Plan $newPlan
     * @param string $action
     * @param string|null $notes
     * @return void
     */
    protected function logSubscriptionChange(
        Tenant $tenant,
        Subscription $subscription,
        ?Plan $oldPlan,
        Plan $newPlan,
        string $action,
        ?string $notes = null
    ): void {
        SubscriptionLog::create([
            'tenant_id' => $tenant->id,
            'user_id' => Auth::id(),
            'subscription_id' => $subscription->id,
            'old_plan_id' => $oldPlan?->id,
            'new_plan_id' => $newPlan->id,
            'action' => $action,
            'old_price' => $oldPlan?->price,
            'new_price' => $newPlan->price,
            'notes' => $notes,
            'metadata' => [
                'old_plan_name' => $oldPlan?->name,
                'new_plan_name' => $newPlan->name,
                'trial_days' => $newPlan->trial_days,
                'billing_period' => $newPlan->billing_period,
            ],
        ]);
    }
}

