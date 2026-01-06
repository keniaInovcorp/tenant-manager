@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Utilizadores - {{ $tenant->name }}</h1>
        @if($tenant->canManageUsers(Auth::user()) && $tenant->canAddUsers())
            <a href="{{ route('tenants.users.create', $tenant) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                + Adicionar Utilizador
            </a>
        @elseif($tenant->canManageUsers(Auth::user()) && !$tenant->canAddUsers())
            @php
                $plan = $tenant->currentPlan();
                $limit = $plan ? $plan->getLimit('users') : 0;
            @endphp
            <div class="text-sm text-gray-600 dark:text-gray-400">
                Limite de utilizadores atingido ({{ $limit }}). <a href="{{ route('subscriptions.index') }}" class="text-blue-600 hover:underline">Fazer upgrade</a>
            </div>
        @endif
    </div>


    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Nome</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Função</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Ações</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($users as $user)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                            {{ $user->name }}
                            @if($tenant->isOwner($user))
                                <span class="ml-2 text-xs text-blue-600 dark:text-blue-400">(Proprietário)</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $user->email }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            @php
                                $role = is_object($user->pivot) && isset($user->pivot->role) ? $user->pivot->role : ($tenant->isOwner($user) ? 'owner' : 'member');
                                $roleLabels = [
                                    'owner' => 'Proprietário',
                                    'admin' => 'Administrador',
                                    'member' => 'Membro'
                                ];
                                $roleLabel = $roleLabels[$role] ?? ucfirst($role);
                            @endphp
                            <span class="px-2 py-1 text-xs rounded-full {{ $role === 'owner' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : ($role === 'admin' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200') }}">
                                {{ $roleLabel }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            @if(!$tenant->isOwner($user))
                                <form action="{{ route('tenants.users.destroy', [$tenant, $user]) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition-all transform hover:scale-110" 
                                            title="Remover utilizador"
                                            onclick="return confirm('Tem certeza que deseja remover este utilizador?')">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            @else
                                <span class="text-gray-400 dark:text-gray-600 text-xs">Não pode remover</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        <a href="{{ route('tenants.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
            ← Voltar para Tenants
        </a>
    </div>
</div>
@endsection

