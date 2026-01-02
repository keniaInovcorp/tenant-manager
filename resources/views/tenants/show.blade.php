@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $tenant->name }}</h1>
        <div class="flex gap-2">
            @php
                $user = Auth::user();
                $canManageUsers = $tenant->canManageUsers($user);
                $canUpdate = $tenant->isOwner($user);
            @endphp
            @if($canManageUsers)
                <a href="{{ route('tenants.users.index', $tenant) }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg">
                    Utilizadores
                </a>
            @endif
            @if($canUpdate)
                <a href="{{ route('tenants.edit', $tenant) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg">
                    Editar
                </a>
            @endif
            <a href="{{ route('tenants.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                Voltar
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <dl class="grid grid-cols-1 gap-4">
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Nome</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $tenant->name }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Slug</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $tenant->slug }}</dd>
            </div>
            @if($tenant->domain)
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Domínio</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $tenant->domain }}</dd>
            </div>
            @endif
            @if($tenant->description)
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Descrição</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $tenant->description }}</dd>
            </div>
            @endif
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Cor Principal</dt>
                <dd class="mt-1">
                    <div class="flex items-center gap-2">
                        <div class="w-10 h-10 rounded-lg border border-gray-300 dark:border-gray-600" 
                             style="background-color: {{ $tenant->settings['branding']['primary_color'] ?? '#3B82F6' }}">
                        </div>
                        <span class="text-sm text-gray-900 dark:text-white font-mono">{{ $tenant->settings['branding']['primary_color'] ?? '#3B82F6' }}</span>
                    </div>
                </dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Estado</dt>
                <dd class="mt-1">
                    <span class="px-2 py-1 text-xs rounded-full {{ $tenant->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                        {{ $tenant->is_active ? 'Ativo' : 'Inativo' }}
                    </span>
                </dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Criado em</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $tenant->created_at->format('d/m/Y H:i') }}</dd>
            </div>
        </dl>
    </div>
</div>
@endsection

