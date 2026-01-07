@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-start mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Planos de Subscrição</h1>
            <p class="text-gray-600 dark:text-gray-400">Escolha o plano ideal para o seu tenant</p>
        </div>
        <div class="flex gap-2">
            @if($currentTenant)
                <a href="{{ route('subscriptions.logs') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Histórico
                </a>
            @endif
            <a href="{{ route('tenants.index') }}" class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors">
                Voltar
            </a>
        </div>
    </div>

    <div>
        @if($currentTenant)
            <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-sm text-blue-800 dark:text-blue-200">
                        <strong>Tenant Atual:</strong> {{ $currentTenant->name }}
                        @if($currentSubscription)
                            • <strong>Plano:</strong> {{ $currentSubscription->plan->name }}
                            @if($currentSubscription->isOnTrial())
                                • <span class="text-orange-600 dark:text-orange-400"> Trial até {{ $currentSubscription->trial_ends_at->format('d/m/Y') }}</span>
                            @endif
                        @else
                            • <span class="text-gray-600 dark:text-gray-400">Nenhum plano ativo</span>
                        @endif
                    </span>
                </div>
            </div>

            @if(!$currentTenant->canManageUsers(Auth::user()))
                <div class="mt-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span class="text-sm text-red-800 dark:text-red-200">
                            <strong>Aviso:</strong> Apenas o proprietário ou administrador pode subscrever ou alterar planos.
                        </span>
                    </div>
                </div>
            @endif
        @else
            <div class="mt-4 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-600 dark:text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span class="text-sm text-yellow-800 dark:text-yellow-200">
                        Selecione um tenant primeiro para subscrever um plano
                    </span>
                </div>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
        @foreach($plans as $plan)
            @php
                $isCurrentPlan = $currentSubscription && $currentSubscription->plan_id === $plan->id;
                $isFeatured = $plan->is_featured;
                $currentPlanPrice = $currentSubscription?->plan->price ?? 0;
                $isUpgrade = $plan->price > $currentPlanPrice && $currentSubscription;
                $isDowngrade = $plan->price < $currentPlanPrice && $currentSubscription;
            @endphp

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden {{ $isFeatured ? 'ring-2 ring-blue-500 dark:ring-blue-400' : 'border border-gray-200 dark:border-gray-700' }} relative">
                @if($isCurrentPlan)
                    <div class="absolute top-0 left-0 bg-green-500 text-white text-xs font-bold px-3 py-1 rounded-br-lg">
                        ✓ PLANO ATUAL
                    </div>
                @endif

                <div class="p-6">
                    <div class="flex justify-between items-center mb-2">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $plan->name }}</h3>
                        @if($isFeatured)
                            <span class="px-3 py-1 bg-blue-600 text-white text-xs font-semibold rounded-full">RECOMENDADO</span>
                        @endif
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">{{ $plan->description }}</p>

                    <div class="mb-4">
                        <span class="text-4xl font-bold text-gray-900 dark:text-white">
                            @if($plan->price == 0)
                                Grátis
                            @else
                                €{{ number_format($plan->price, 2) }}
                            @endif
                        </span>
                        @if($plan->price > 0)
                            <span class="text-gray-600 dark:text-gray-400 text-sm">/mês</span>
                        @endif
                    </div>

                    @if($plan->trial_days > 0)
                        <div class="mb-6 p-3 bg-green-50 dark:bg-green-900/20 border-2 border-green-400 dark:border-green-600 rounded-lg text-center">
                            <div class="flex items-center justify-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-base font-bold text-green-700 dark:text-green-300">
                                    {{ $plan->trial_days }} dias de trial grátis
                                </span>
                            </div>
                        </div>
                    @endif

                    <div class="space-y-3 mb-6">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase">Funcionalidades:</h4>
                        @if(in_array('*', $plan->features ?? []))
                            <div class="flex items-start gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-sm text-gray-700 dark:text-gray-300">Todas as funcionalidades</span>
                            </div>
                        @else
                            @foreach($plan->features ?? [] as $feature)
                                <div class="flex items-start gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="text-sm text-gray-700 dark:text-gray-300 capitalize">{{ str_replace('_', ' ', $feature) }}</span>
                                </div>
                            @endforeach
                        @endif

                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase pt-3">Limites:</h4>
                        @foreach($plan->limits ?? [] as $key => $value)
                            <div class="flex items-start gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-sm text-gray-700 dark:text-gray-300">
                                    <strong class="capitalize">{{ str_replace('_', ' ', $key) }}:</strong>
                                    @if($value == -1)
                                        <span class="text-green-600 dark:text-green-400">Ilimitado</span>
                                    @else
                                        {{ $value }}{{ $key === 'storage' ? ' MB' : '' }}
                                    @endif
                                </span>
                            </div>
                        @endforeach
                    </div>

                    @if($currentTenant)
                        @php
                            $canManage = $currentTenant->canManageUsers(Auth::user());
                        @endphp
                        
                        @if($isCurrentPlan)
                            <button disabled class="w-full py-3 px-4 rounded-lg font-semibold bg-gray-300 dark:bg-gray-700 text-gray-500 dark:text-gray-400 cursor-not-allowed">
                                Plano Atual
                            </button>
                        @elseif(!$canManage)
                            <button disabled class="w-full py-3 px-4 rounded-lg font-semibold bg-gray-300 dark:bg-gray-700 text-gray-500 dark:text-gray-400 cursor-not-allowed" title="Apenas proprietário ou administrador pode subscrever">
                                Sem Permissão
                            </button>
                        @elseif($isUpgrade)
                            <form method="POST" action="{{ route('tenants.upgrade', $currentTenant) }}">
                                @csrf
                                <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                <button type="submit" class="w-full py-3 px-4 rounded-lg font-semibold bg-green-600 hover:bg-green-700 text-white transition-colors">
                                    Upgrade para {{ $plan->name }}
                                </button>
                            </form>
                        @elseif($isDowngrade)
                            <form method="POST" action="{{ route('tenants.downgrade', $currentTenant) }}">
                                @csrf
                                <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                <button type="submit" class="w-full py-3 px-4 rounded-lg font-semibold bg-orange-600 hover:bg-orange-700 text-white transition-colors">
                                    Downgrade para {{ $plan->name }}
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('tenants.subscribe', $currentTenant) }}">
                                @csrf
                                <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                <button type="submit" class="w-full py-3 px-4 rounded-lg font-semibold {{ $isFeatured ? 'bg-blue-600 hover:bg-blue-700' : 'bg-gray-800 hover:bg-gray-900 dark:bg-gray-700 dark:hover:bg-gray-600' }} text-white transition-colors">
                                    Subscrever {{ $plan->name }}
                                </button>
                            </form>
                        @endif
                    @else
                        <button disabled class="w-full py-3 px-4 rounded-lg font-semibold bg-gray-300 dark:bg-gray-700 text-gray-500 dark:text-gray-400 cursor-not-allowed">
                            Selecione um Tenant
                        </button>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    @if($currentTenant && $currentSubscription)
        <div class="mt-8 p-6 bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4"> Detalhes da Subscrição Atual</h2>

            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Plano</dt>
                    <dd class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">{{ $currentSubscription->plan->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                    <dd class="mt-1">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            {{ $currentSubscription->isActive() ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300' }}">
                            {{ $currentSubscription->isActive() ? '✓ Ativa' : '✗ Inativa' }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Preço</dt>
                    <dd class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">€{{ number_format($currentSubscription->plan->price, 2) }}/mês</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Início</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $currentSubscription->starts_at->format('d/m/Y H:i') }}</dd>
                </div>
                @if($currentSubscription->ends_at)
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Fim</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $currentSubscription->ends_at->format('d/m/Y H:i') }}</dd>
                    </div>
                @else
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Duração</dt>
                        <dd class="mt-1 text-sm text-green-600 dark:text-green-400 font-semibold">Ilimitado</dd>
                    </div>
                @endif
                @if($currentSubscription->trial_ends_at)
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Trial Termina</dt>
                        <dd class="mt-1 text-sm {{ $currentSubscription->isOnTrial() ? 'text-orange-600 dark:text-orange-400 font-semibold' : 'text-gray-900 dark:text-white' }}">
                            {{ $currentSubscription->trial_ends_at->format('d/m/Y H:i') }}
                            @if($currentSubscription->isOnTrial())
                                @php
                                    $diffInDays = now()->diffInDays($currentSubscription->trial_ends_at);
                                    $diffInWeeks = intdiv($diffInDays, 7);
                                    
                                    if ($diffInWeeks > 0) {
                                        $timeMessage = $diffInWeeks === 1 ? '1 semana a partir de agora' : "$diffInWeeks semanas a partir de agora";
                                    } else {
                                        $timeMessage = $diffInDays === 1 ? '1 dia a partir de agora' : "$diffInDays dias a partir de agora";
                                    }
                                @endphp
                                ({{ $timeMessage }})
                            @endif
                        </dd>
                    </div>
                @endif
            </dl>
        </div>
    @endif
</div>
@endsection
