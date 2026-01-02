<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TenantController extends Controller
{
    /**
     * Display a listing of the user's tenants.
     *
     * @return View
     */
    public function index(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Get tenant IDs where user is owner
        $ownedTenantIds = $user->ownedTenants()->pluck('id');

        // Get tenant IDs where user is member/admin
        $associatedTenantIds = $user->tenants()->pluck('tenants.id');

        $allTenantIds = $ownedTenantIds->merge($associatedTenantIds)->unique();

        // Get all tenants
        $tenants = Tenant::whereIn('id', $allTenantIds)
            ->latest()
            ->paginate(10);

        return view('tenants.index', compact('tenants'));
    }

    /**
     * Show the form for creating a new tenant.
     *
     * @return View
     */
    public function create(): View
    {
        return view('tenants.create');
    }

    /**
     * Store a newly created tenant in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'domain' => 'nullable|string|max:255|unique:tenants,domain',
            'description' => 'nullable|string|max:1000',
        ]);

        $tenant = Tenant::create([
            'owner_id' => Auth::id(),
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'domain' => $validated['domain'] ?? null,
            'description' => $validated['description'] ?? null,
            'is_active' => true,
        ]);

        return redirect()->route('tenants.index')
            ->with('success', 'Tenant criado com sucesso!');
    }

    /**
     * Display the specified tenant.
     *
     * @param Tenant $tenant
     * @return View
     */
    public function show(Tenant $tenant): View
    {
        $this->authorize('view', $tenant);
        return view('tenants.show', compact('tenant'));
    }

    /**
     * Show the form for editing the specified tenant.
     *
     * @param Tenant $tenant
     * @return View
     */
    public function edit(Tenant $tenant): View
    {
        $this->authorize('update', $tenant);
        return view('tenants.edit', compact('tenant'));
    }

    /**
     * Update the specified tenant in storage.
     *
     * @param Request $request
     * @param Tenant $tenant
     * @return RedirectResponse
     */
    public function update(Request $request, Tenant $tenant): RedirectResponse
    {
        $this->authorize('update', $tenant);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'domain' => 'nullable|string|max:255|unique:tenants,domain,' . $tenant->id,
            'description' => 'nullable|string|max:1000',
            'is_active' => 'sometimes|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') && $request->is_active == '1';

        $tenant->update($validated);

        return redirect()->route('tenants.index')
            ->with('success', 'Tenant atualizado com sucesso!');
    }
}
