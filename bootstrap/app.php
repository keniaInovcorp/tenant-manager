<?php

use App\Http\Middleware\SetTenantContext;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            SetTenantContext::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule): void {
        $schedule->command('trial:check-expirations')
            ->dailyAt('09:00')
            ->timezone('Europe/Lisbon');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
