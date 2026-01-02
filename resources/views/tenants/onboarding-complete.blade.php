@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-8 text-center">
        <div class="mb-6">
            <svg class="mx-auto h-16 w-16 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
            Tenant Criado com Sucesso!
        </h1>
        
        <p class="text-gray-600 dark:text-gray-400 mb-2">
            Bem-vindo ao <span class="font-semibold text-blue-600 dark:text-blue-400">{{ tenant()?->name }}</span>!
        </p>
        
        <p class="text-sm text-gray-500 dark:text-gray-500 mb-8">
            O teu tenant foi configurado e está pronto para usar.
        </p>

        <div class="space-y-3">
            <a href="{{ route('tenants.show', tenant()) }}" class="block w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition-colors">
                Ver Detalhes do Tenant
            </a>
            
            <a href="{{ route('tenants.index') }}" class="block w-full bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-white px-6 py-3 rounded-lg transition-colors">
                Ver Todos os Meus Tenants
            </a>
        </div>

        <div class="mt-8 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
            <h3 class="text-sm font-semibold text-blue-900 dark:text-blue-300 mb-2">Próximos Passos:</h3>
            <ul class="text-sm text-blue-800 dark:text-blue-400 space-y-1">
                <li>✓ Tenant criado</li>
                <li>→ Adicionar utilizadores à equipa</li>
                <li>→ Configurar permissões</li>
                <li>→ Personalizar definições</li>
            </ul>
        </div>
    </div>
</div>
@endsection

