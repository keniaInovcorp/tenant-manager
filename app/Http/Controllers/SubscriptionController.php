<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Tenant;
use App\Models\User;
use App\Services\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Controller for managing tenant subscriptions.
 */
class SubscriptionController extends Controller
{
    /**
     * Create a new SubscriptionController instance.
     *
     * @param SubscriptionService $subscriptionService
     */
    public function __construct(
        protected SubscriptionService $subscriptionService
    ) {}

    /**
     * Display a listing of the available plans.
     *
     * @return View|RedirectResponse
     */
    public function index(): View|RedirectResponse
    {
        $currentTenant = tenant();
        
        if (!$currentTenant) {
            return redirect()->route('tenants.index')
                ->with('error', 'Selecione ou crie um tenant primeiro para ver os planos.');
        }

        $plans = Plan::where('is_active', true)->orderBy('sort_order')->get();
        $currentSubscription = $currentTenant->subscription;

        return view('subscriptions.index', compact('plans', 'currentTenant', 'currentSubscription'));
    }

    /**
     * Subscribe a tenant to a plan.
     *
     * @param Request $request
     * @param Tenant $tenant
     * @return RedirectResponse
     */
    public function subscribe(Request $request, Tenant $tenant): RedirectResponse
    {
        $validation = $this->validateSubscriptionChange($tenant, $request->plan_id);
        if ($validation instanceof RedirectResponse) {
            return $validation;
        }

        $this->subscriptionService->subscribe($tenant, $validation);

        return redirect()->back()->with('success', 'Subscrição criada com sucesso!');
    }

    /**
     * Upgrade a tenant's subscription to a new plan.
     *
     * @param Request $request
     * @param Tenant $tenant
     * @return RedirectResponse
     */
    public function upgrade(Request $request, Tenant $tenant): RedirectResponse
    {
        $validation = $this->validateSubscriptionChange($tenant, $request->plan_id);
        if ($validation instanceof RedirectResponse) {
            return $validation;
        }

        $this->subscriptionService->upgrade($tenant, $validation);

        return redirect()->back()->with('success', 'Upgrade realizado com sucesso!');
    }

    /**
     * Downgrade a tenant's subscription to a new plan.
     *
     * @param Request $request
     * @param Tenant $tenant
     * @return RedirectResponse
     */
    public function downgrade(Request $request, Tenant $tenant): RedirectResponse
    {
        $validation = $this->validateSubscriptionChange($tenant, $request->plan_id);
        if ($validation instanceof RedirectResponse) {
            return $validation;
        }

        $this->subscriptionService->downgrade($tenant, $validation);

        return redirect()->back()->with('success', 'Downgrade realizado com sucesso!');
    }

    /**
     * Validate permissions and plan compatibility for a subscription change.
     *
     * @param Tenant $tenant
     * @param int $planId
     * @return Plan|RedirectResponse
     */
    private function validateSubscriptionChange(Tenant $tenant, int $planId): Plan|RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$tenant->canManageUsers($user)) {
            return redirect()->back()->with('error', 'Apenas o proprietário ou administrador pode alterar planos.');
        }

        $plan = Plan::findOrFail($planId);
        
        $compatibility = $tenant->isCompatibleWithPlan($plan);
        if (!$compatibility['compatible']) {
            return redirect()->back()->with('error', implode(' ', $compatibility['issues']));
        }

        return $plan;
    }

}
