@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Meus Tenants</h1>
        <a href="{{ route('tenants.onboarding') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
            + Criar Tenant
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Nome</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Plano</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Utilizadores</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Slug</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Ações</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($tenants as $tenant)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                            {{ $tenant->name }}
                            @if($tenant->isOwner(Auth::user()))
                                <span class="ml-2 text-xs text-blue-600 dark:text-blue-400">(Proprietário)</span>
                            @else
                                @php
                                    $userRole = Auth::user()->tenants()->where('tenants.id', $tenant->id)->first()?->pivot->role ?? null;
                                    $roleLabels = [
                                        'admin' => 'Administrador',
                                        'member' => 'Membro'
                                    ];
                                    $roleLabel = $roleLabels[$userRole] ?? '';
                                @endphp
                                @if($roleLabel)
                                    <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">({{ $roleLabel }})</span>
                                @endif
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $plan = $tenant->currentPlan();
                            @endphp
                            @if($plan)
                                <span class="px-2 py-1 text-xs font-semibold rounded {{ 
                                    $plan->name === 'Free' ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200' : 
                                    ($plan->name === 'Pro' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 
                                    'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200') 
                                }}">
                                    {{ $plan->name }}
                                </span>
                            @else
                                <span class="text-xs text-gray-500 dark:text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            @php
                                $userCount = $tenant->users()->count();
                                $plan = $tenant->currentPlan();
                                $limit = $plan ? $plan->getLimit('users') : 0;
                            @endphp
                            <div class="flex items-center gap-2">
                                <span class="font-medium">{{ $userCount }}</span>
                                @if($limit !== -1)
                                    <span class="text-xs text-gray-500 dark:text-gray-400">/ {{ $limit }}</span>
                                @else
                                    <span class="text-xs text-gray-500 dark:text-gray-400">/ ∞</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $tenant->slug }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full {{ $tenant->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                {{ $tenant->is_active ? 'Ativo' : 'Inativo' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('tenants.show', $tenant) }}" 
                                   class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-all transform hover:scale-110 hover:rotate-3" 
                                   title="Ver detalhes">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                @if($tenant->isOwner(Auth::user()))
                                    <a href="{{ route('tenants.edit', $tenant) }}" 
                                       class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 transition-all transform hover:scale-110 hover:rotate-3" 
                                       title="Editar">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">Nenhum tenant encontrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $tenants->links() }}
    </div>
</div>
@endsection

