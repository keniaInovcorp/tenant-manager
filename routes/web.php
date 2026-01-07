<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SubscriptionLogController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\TenantOnboardingController;
use App\Http\Controllers\TenantUserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('home');
    }
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/home', function () {
        return redirect()->route('dashboard');
    })->name('home');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('tenants')->group(function () {
        Route::get('/onboarding', [TenantOnboardingController::class, 'show'])->name('tenants.onboarding');
        Route::post('/onboarding', [TenantOnboardingController::class, 'store'])->name('tenants.onboarding.store');
        Route::get('/onboarding/complete', [TenantOnboardingController::class, 'complete'])->name('tenants.onboarding.complete');
    });

    Route::resource('tenants', TenantController::class)->except(['destroy']);
    Route::resource('tenants.users', TenantUserController::class)->only(['index', 'create', 'store', 'destroy']);

    Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::post('/tenants/{tenant}/subscribe', [SubscriptionController::class, 'subscribe'])->name('tenants.subscribe');
    Route::post('/tenants/{tenant}/upgrade', [SubscriptionController::class, 'upgrade'])->name('tenants.upgrade');
    Route::post('/tenants/{tenant}/downgrade', [SubscriptionController::class, 'downgrade'])->name('tenants.downgrade');
    
    Route::get('/subscriptions/logs', [SubscriptionLogController::class, 'index'])->name('subscriptions.logs');
    Route::get('/tenants/{tenant}/subscriptions/logs', [SubscriptionLogController::class, 'index'])->name('tenants.subscriptions.logs');
});
