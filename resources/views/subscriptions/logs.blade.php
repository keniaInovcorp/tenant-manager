@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Histórico de Subscrições</h1>
            <p class="text-gray-600 dark:text-gray-400">Tenant: <strong>{{ $tenant->name }}</strong></p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('subscriptions.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                Ver Planos
            </a>
            <a href="{{ route('tenants.index') }}" class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-white px-4 py-2 rounded-lg transition-colors">
                Voltar
            </a>
        </div>
    </div>

    @if($logs->count() > 0)
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Data/Hora
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Ação
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Plano Anterior
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Plano Novo
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Preço
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Utilizador
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Notas
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($logs as $log)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    <div>{{ $log->created_at->format('d/m/Y') }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $log->created_at->format('H:i:s') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $log->actionColor }}">
                                        {{ $log->actionLabel }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    @if($log->oldPlan)
                                        <div class="font-medium">{{ $log->oldPlan->name }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">€{{ number_format($log->old_price, 2) }}</div>
                                    @else
                                        <span class="text-gray-400 dark:text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    @if($log->newPlan)
                                        <div class="font-medium">{{ $log->newPlan->name }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">€{{ number_format($log->new_price, 2) }}</div>
                                    @else
                                        <span class="text-gray-400 dark:text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    @if($log->old_price && $log->new_price)
                                        @php
                                            $diff = $log->new_price - $log->old_price;
                                        @endphp
                                        @if($diff > 0)
                                            <span class="text-green-600 dark:text-green-400 font-medium">+€{{ number_format($diff, 2) }}</span>
                                        @elseif($diff < 0)
                                            <span class="text-red-600 dark:text-red-400 font-medium">-€{{ number_format(abs($diff), 2) }}</span>
                                        @else
                                            <span class="text-gray-500 dark:text-gray-400">€0.00</span>
                                        @endif
                                    @else
                                        <span class="text-gray-400 dark:text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                    @if($log->user)
                                        <div>{{ $log->user->name }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $log->user->email }}</div>
                                    @else
                                        <span class="text-gray-400 dark:text-gray-500">Sistema</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                    {{ $log->notes ?? '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $logs->links() }}
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-12 border border-gray-200 dark:border-gray-700">
            <div class="text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Nenhum Histórico Disponível</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-6">
                    Este tenant ainda não tem alterações de plano registadas.
                </p>
                <a href="{{ route('subscriptions.index') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition-colors font-medium">
                    Ver Planos Disponíveis
                </a>
            </div>
        </div>
    @endif

    @if(tenant() && tenant()->subscription)
        <div class="mt-8 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
            <div class="flex items-start gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 dark:text-blue-400 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="flex-1">
                    <h4 class="text-sm font-semibold text-blue-900 dark:text-blue-200 mb-1">Plano Atual</h4>
                    <p class="text-sm text-blue-800 dark:text-blue-300">
                        Está atualmente no plano <strong>{{ tenant()->currentPlan()?->name }}</strong>.
                        @if(tenant()->subscription->isOnTrial())
                            O período de trial termina em <strong>{{ tenant()->subscription->trial_ends_at->format('d/m/Y') }}</strong>.
                        @endif
                    </p>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

