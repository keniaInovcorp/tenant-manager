@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $tenant->name }}</h1>
        <div class="flex gap-2">
            <a href="{{ route('tenants.edit', $tenant) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg">
                Editar
            </a>
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
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Estado</dt>
                <dd class="mt-1">
                    <span class="px-2 py-1 text-xs rounded-full {{ $tenant->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
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

