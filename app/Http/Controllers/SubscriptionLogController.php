<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SubscriptionLogController extends Controller
{
    /**
     * Display subscription logs for the current tenant or a specific tenant.
     *
     * @param Tenant|null $tenant
     * @return View|RedirectResponse
     */
    public function index(?Tenant $tenant = null): View|RedirectResponse
    {
        $tenant = $tenant ?? tenant();

        if (!$tenant) {
            return redirect()->route('tenants.index')
                ->with('error', 'Selecione um tenant primeiro para ver os logs.');
        }

        $this->authorize('view', $tenant);

        $logs = $tenant->subscriptionLogs()
            ->with(['user', 'oldPlan', 'newPlan'])
            ->latest()
            ->paginate(20);

        return view('subscriptions.logs', compact('tenant', 'logs'));
    }
}

