@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Dashboard</h1>

    @if($tenant)
        @php
            $brandColor = $tenant->settings['branding']['primary_color'] ?? '#3B82F6';
        @endphp
        <div class="mb-6 bg-white dark:bg-gray-800 shadow rounded-lg p-6 border-l-4 border-gray-200 dark:border-gray-700" style="border-left-color: {{ $brandColor }};">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
                <span style="color: {{ $brandColor }};">●</span> Tenant Ativo: {{ $tenant->name }}
            </h2>
            @if($plan)
                <div class="space-y-2">
                    <p class="text-gray-700 dark:text-gray-300">
                        Plano:
                        <span class="px-2 py-1 text-xs font-semibold rounded-full text-white" style="background-color: {{ $brandColor }};">
                            {{ $plan->name }}
                        </span>
                    </p>
                    @if($subscription->isOnTrial())
                        <div class="flex items-center gap-2 p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-600 dark:text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-sm text-yellow-800 dark:text-yellow-200">
                                <strong>Trial até:</strong> {{ $subscription->trial_ends_at->format('d/m/Y H:i') }}
                            </span>
                        </div>
                    @endif
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400">Nenhum plano ativo</p>
            @endif
        </div>

        @if($plan && !empty($usage))
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Utilização de Recursos</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($usage as $feature => $data)
                        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 border-l-4 border-gray-200 dark:border-gray-700" style="border-left-color: {{ $brandColor }};">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 capitalize">
                                <span style="color: {{ $brandColor }};">●</span> {{ str_replace('_', ' ', $feature) }}
                            </h3>
                            <div class="mb-2">
                                <div class="flex justify-between mb-2">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Utilizado</span>
                                    <span class="font-bold text-gray-900 dark:text-white">
                                        {{ $data['used'] }} / {{ $data['limit'] === -1 ? '∞' : $data['limit'] }}
                                    </span>
                                </div>
                                @if($data['limit'] > 0)
                                    @php
                                        $percentage = min(100, ($data['used'] / $data['limit']) * 100);
                                        $progressColor = $percentage >= 90 ? '#DC2626' : ($percentage >= 75 ? '#D97706' : $brandColor);
                                    @endphp
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                                        <div class="h-2.5 rounded-full transition-all duration-300"
                                             style="width: {{ $percentage }}%; background-color: {{ $progressColor }};"></div>
                                    </div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                                        Restam: <strong class="text-gray-900 dark:text-white">{{ $data['remaining'] }}</strong>
                                    </p>
                                    @if($percentage >= 90)
                                        <div class="mt-3 p-2 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded">
                                            <p class="text-xs text-red-800 dark:text-red-200 flex items-center gap-1">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    class="w-4 h-4 flex-shrink-0"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M8.257 3.099c.765-1.36 2.72-1.36 3.485 0l6.518 11.59c.75 1.334-.213 2.99-1.742 2.99H3.48c-1.53 0-2.492-1.656-1.742-2.99L8.257 3.1zM11 14a1 1 0 10-2 0 1 1 0 002 0zm-1-7a1 1 0 00-.993.883L9 8v4a1 1 0 001.993.117L11 12V8a1 1 0 00-1-1z"
                                                        clip-rule="evenodd" />
                                                </svg>

                                                <span>
                                                    Limite quase atingido.
                                                    <a href="{{ route('subscriptions.index') }}"
                                                    class="underline hover:text-red-900 dark:hover:text-red-100">
                                                        Fazer upgrade
                                                    </a>
                                                </span>
                                            </p>
                                        </div>
                                    @elseif($percentage >= 75)
                                        <div class="mt-3 p-2 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded">
                                            <p class="text-xs text-yellow-800 dark:text-yellow-200 flex items-center gap-1">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    class="w-4 h-4 flex-shrink-0"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M8.257 3.099c.765-1.36 2.72-1.36 3.485 0l6.518 11.59c.75 1.334-.213 2.99-1.742 2.99H3.48c-1.53 0-2.492-1.656-1.742-2.99L8.257 3.1zM11 14a1 1 0 10-2 0 1 1 0 002 0zm-1-7a1 1 0 00-.993.883L9 8v4a1 1 0 001.993.117L11 12V8a1 1 0 00-1-1z"
                                                        clip-rule="evenodd" />
                                                </svg>

                                                Aproximando do limite. Considere fazer upgrade.
                                            </p>
                                        </div>
                                    @endif
                                @else
                                    <div class="flex items-center gap-2 p-2 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span class="text-sm text-green-700 dark:text-green-300 font-medium">Ilimitado</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex justify-center gap-4">
                <a href="{{ route('subscriptions.index') }}" class="text-white px-6 py-3 rounded-lg transition-all font-medium hover:opacity-90 shadow-md" style="background-color: {{ $brandColor }};">
                    Ver Planos Disponíveis
                </a>
                <a href="{{ route('tenants.users.index', $tenant) }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition-colors font-medium shadow-md">
                    Gerir Utilizadores
                </a>
            </div>
        @endif
    @else
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 border border-gray-200 dark:border-gray-700">
            <div class="text-center py-8">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <p class="text-gray-500 dark:text-gray-400 mb-4">Nenhum tenant ativo. Crie um tenant para começar.</p>
                <a href="{{ route('tenants.onboarding') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition-colors font-medium">
                    Criar Primeiro Tenant
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
