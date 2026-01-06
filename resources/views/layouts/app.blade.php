<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Tenant Manager') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <nav class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center gap-8">
                    <a href="/" class="text-xl font-bold text-gray-900 dark:text-white">Tenant Manager</a>
                    @auth
                        <div class="flex items-center gap-6 pl-8">
                            <a href="{{ route('dashboard') }}" class="text-gray-700 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition-colors font-medium">
                                Dashboard
                            </a>
                            <a href="{{ route('tenants.index') }}" class="text-gray-700 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition-colors font-medium">
                                Tenants
                            </a>
                            @if(tenant())
                                <a href="{{ route('subscriptions.index') }}" class="text-gray-700 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition-colors font-medium">
                                    Planos
                                </a>
                            @endif
                        </div>
                    @endauth
                </div>
                <div class="flex items-center gap-4">
                    @auth
                        <tenant-switcher />
                        <div id="user-menu-container" data-user-name="{{ Auth::user()->name }}">
                            <script>
                                window.currentUserName = "{{ Auth::user()->name }}";
                            </script>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </nav>
    <main class="py-8">
        @if(session('success'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-4">
                <div id="flash-success" class="flash-message bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-200 px-4 py-3 rounded transition-opacity duration-500">
                    {{ session('success') }}
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-4">
                <div id="flash-error" class="flash-message bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-200 px-4 py-3 rounded transition-opacity duration-500">
                    {{ session('error') }}
                </div>
            </div>
        @endif
        @yield('content')
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const flashMessages = document.querySelectorAll('.flash-message');

            flashMessages.forEach(function(message) {
                setTimeout(function() {
                    message.style.opacity = '0';
                    setTimeout(function() {
                        message.style.display = 'none';
                    }, 500);
                }, 5000);
            });
        });
    </script>
    <div id="vue-app-container"></div>
</body>
</html>
