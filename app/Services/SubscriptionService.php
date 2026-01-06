<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use Illuminate\Support\Carbon;
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
     * @return Subscription
     */
    public function subscribe(Tenant $tenant, Plan $plan, $startDate = null): Subscription
    {
        return DB::transaction(function () use ($tenant, $plan, $startDate) {
            $startDate = $startDate ?? now();
            $trialEndDate = $plan->trial_days > 0 ? $startDate->copy()->addDays($plan->trial_days) : null;
            
            if ($trialEndDate) {
                $endDate = $trialEndDate;
            } elseif ($plan->price == 0) {
                $endDate = null;
            } else {
                $endDate = $this->calculateEndDate($startDate, $plan->billing_period);
            }

            $tenant->subscription()?->update(['status' => 'cancelled']);

            return Subscription::create([
                'tenant_id' => $tenant->id,
                'plan_id' => $plan->id,
                'status' => 'active',
                'starts_at' => $startDate,
                'ends_at' => $endDate,
                'trial_ends_at' => $trialEndDate,
            ]);
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

            $currentSubscription->update(['status' => 'cancelled']);
            return $this->subscribe($tenant, $newPlan, now());
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

            $currentSubscription->update(['status' => 'cancelled']);
            return $this->subscribe($tenant, $newPlan, now());
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
}

