@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Editar Tenant</h1>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <form action="{{ route('tenants.update', $tenant) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Nome *
                </label>
                <input type="text" name="name" value="{{ old('name', $tenant->name) }}" required
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Domínio (Opcional)
                </label>
                <input type="text" name="domain" value="{{ old('domain', $tenant->domain) }}"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                @error('domain')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Descrição
                </label>
                <textarea name="description" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">{{ old('description', $tenant->description) }}</textarea>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Cor Principal
                </label>
                <div class="flex items-center gap-3">
                    <input type="color" name="primary_color" value="{{ old('primary_color', $tenant->settings['branding']['primary_color'] ?? '#3B82F6') }}"
                           class="h-10 w-20 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Cor usada para branding do tenant</span>
                </div>
                @error('primary_color')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $tenant->is_active) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Ativo</span>
                </label>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                    Atualizar Tenant
                </button>
                <a href="{{ route('tenants.show', $tenant) }}" class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-white px-4 py-2 rounded-lg transition-colors">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

