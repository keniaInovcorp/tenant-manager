<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTenantRequest;
use App\Services\TenantService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Controller for tenant onboarding process.
 *
 * Handles the self-service tenant creation workflow.
 */
class TenantOnboardingController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param TenantService $tenantService
     */
    public function __construct(
        protected TenantService $tenantService
    ) {}

    /**
     * Show the tenant onboarding form.
     *
     * @return View
     */
    public function show(): View
    {
        return view('tenants.onboarding');
    }

    /**
     * Store a new tenant from the onboarding form.
     *
     * @param CreateTenantRequest $request
     * @return RedirectResponse
     */
    public function store(CreateTenantRequest $request): RedirectResponse
    {
        $user = Auth::user();

        $tenant = $this->tenantService->createTenant($user, [
            'name' => $request->name,
            'domain' => $request->domain,
            'description' => $request->description,
            'settings' => [
                'branding' => [
                    'primary_color' => $request->primary_color ?? '#3B82F6',
                ],
            ],
        ]);

        session(['tenant_id' => $tenant->id]);

        return redirect()->route('tenants.onboarding.complete')
            ->with('success', 'Tenant criado com sucesso!');
    }

    /**
     * Show the onboarding completion page.
     *
     * @return View
     */
    public function complete(): View
    {
        return view('tenants.onboarding-complete');
    }
}
