<?php

use App\Http\Controllers\SubscriptionController;
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
        return redirect()->route('tenants.index');
    })->name('home');

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
});
