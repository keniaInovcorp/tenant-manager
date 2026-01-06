<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TenantUserController extends Controller
{
    /**
     * Display a listing of users for the specified tenant.
     *
     * @param Tenant $tenant
     * @return View
     */
    public function index(Tenant $tenant): View
    {
        $this->authorize('view', $tenant);

        // Get users
        $pivotUsers = $tenant->users()->withPivot('role', 'permissions')->get();

        // Get owner and ensure it's included in the list
        $owner = $tenant->owner;
        $users = $pivotUsers;

        if ($owner && !$pivotUsers->contains('id', $owner->id)) {
            $owner->pivot = (object) [
                'role' => 'owner',
                'permissions' => ['*'],
                'tenant_id' => $tenant->id,
                'user_id' => $owner->id,
            ];
            $users = $pivotUsers->push($owner);
        }

        return view('tenants.users.index', compact('tenant', 'users'));
    }

    /**
     * Show the form for creating a new tenant user.
     *
     * @param Tenant $tenant
     * @return View|RedirectResponse
     */
    public function create(Tenant $tenant): View|RedirectResponse
    {
        $this->authorize('manageUsers', $tenant);

        if (!$tenant->canAddUsers()) {
            $plan = $tenant->currentPlan();
            $limit = $plan ? $plan->getLimit('users') : 0;
            return redirect()->route('tenants.users.index', $tenant)
                ->with('error', "Limite de utilizadores atingido ({$limit}). Faça upgrade do plano para adicionar mais utilizadores.");
        }

        // Get all user IDs already associated with this tenant
        $associatedUserIds = $tenant->users()->pluck('user_id')->toArray();
        if ($tenant->owner_id) {
            $associatedUserIds[] = $tenant->owner_id;
        }

        $availableUsers = User::whereNotIn('id', array_unique($associatedUserIds))->get();

        return view('tenants.users.create', compact('tenant', 'availableUsers'));
    }

    /**
     * Store a newly created tenant user.
     *
     * @param Request $request
     * @param Tenant $tenant
     * @return RedirectResponse
     */
    public function store(Request $request, Tenant $tenant): RedirectResponse
    {
        $this->authorize('manageUsers', $tenant);

        if (!$tenant->canAddUsers()) {
            $plan = $tenant->currentPlan();
            $limit = $plan ? $plan->getLimit('users') : 0;
            return redirect()->route('tenants.users.index', $tenant)
                ->with('error', "Limite de utilizadores atingido ({$limit}). Faça upgrade do plano para adicionar mais utilizadores.");
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:admin,member',
        ]);

        $tenant->users()->attach($validated['user_id'], [
            'role' => $validated['role'],
        ]);

        return redirect()->route('tenants.users.index', $tenant)
            ->with('success', 'Utilizador adicionado com sucesso!');
    }

    /**
     * Remove the specified user from the tenant.
     *
     * @param Tenant $tenant
     * @param User $user
     * @return RedirectResponse
     */
    public function destroy(Tenant $tenant, User $user): RedirectResponse
    {
        $this->authorize('manageUsers', $tenant);

        if ($tenant->isOwner($user)) {
            return redirect()->back()->with('error', 'Não pode remover o proprietário!');
        }

        $tenant->users()->detach($user->id);
        return redirect()->back()->with('success', 'Utilizador removido com sucesso!');
    }
}
