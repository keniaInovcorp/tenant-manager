<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Services\SubscriptionService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected SubscriptionService $subscriptionService
    ) {}

    /**
     * Display the dashboard with tenant usage and subscription information.
     *
     * @return View
     */
    public function index(): View
    {
        $tenant = tenant();
        $subscription = $tenant?->subscription;
        $plan = $subscription?->plan;

        $usage = [];
        if ($subscription && $plan) {
            foreach ($plan->limits ?? [] as $feature => $limit) {
                $usage[$feature] = [
                    'limit' => $limit,
                    'used' => $this->getUsage($tenant, $feature),
                    'remaining' => $limit === -1 ? -1 : max(0, $limit - $this->getUsage($tenant, $feature)),
                ];
            }
        }

        return view('dashboard', compact('tenant', 'subscription', 'plan', 'usage'));
    }

    /**
     * Get the current usage for a specific feature.
     *
     * @param Tenant|null $tenant
     * @param string $feature
     * @return int
     */
    protected function getUsage(?Tenant $tenant, string $feature): int
    {
        if (!$tenant) {
            return 0;
        }

        return match($feature) {
            'users' => $tenant->users()->count(),
            'storage' => 0,
            default => 0,
        };
    }
}

